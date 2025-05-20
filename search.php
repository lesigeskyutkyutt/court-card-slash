<?php
header('Content-Type: application/json');

// Get the search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Debug output
error_log("Search query: " . $query);

if (empty($query)) {
    echo json_encode([]);
    exit;
}

// Initialize search results array
$searchResults = [];

// Try to connect to the database
try {
    // Try different database names
    $dbNames = ['game_db', 'login_system', 'mysql'];
    $db = null;
    
    foreach ($dbNames as $dbName) {
        $db = new mysqli('localhost', 'root', '', $dbName);
        if (!$db->connect_error) {
            error_log("Connected to database: " . $dbName);
            break;
        }
    }
    
    if ($db && !$db->connect_error) {
        // Check if tables exist
        $tables = ['forum_posts', 'patch_notes', 'game_info'];
        $existingTables = [];
        
        foreach ($tables as $table) {
            $result = $db->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                $existingTables[] = $table;
                error_log("Table '$table' exists.");
            } else {
                error_log("Table '$table' does not exist.");
            }
        }
        
        // Only proceed with search if tables exist
        if (!empty($existingTables)) {
            // Prepare the search query
            $searchQuery = "%{$query}%";
            
            // Build the SQL query dynamically based on existing tables
            $sql = "";
            $params = [];
            $types = "";
            
            if (in_array('forum_posts', $existingTables)) {
                $sql .= "SELECT 'forum_post' as type, title, content as description, CONCAT('community-forum.php?id=', id) as url FROM forum_posts WHERE title LIKE ? OR content LIKE ?";
                $params[] = $searchQuery;
                $params[] = $searchQuery;
                $types .= "ss";
            }
            
            if (in_array('patch_notes', $existingTables)) {
                if (!empty($sql)) $sql .= " UNION ";
                $sql .= "SELECT 'patch_note' as type, version as title, description, CONCAT('patchnotes.php?id=', id) as url FROM patch_notes WHERE version LIKE ? OR description LIKE ?";
                $params[] = $searchQuery;
                $params[] = $searchQuery;
                $types .= "ss";
            }
            
            if (in_array('game_info', $existingTables)) {
                if (!empty($sql)) $sql .= " UNION ";
                $sql .= "SELECT 'game_info' as type, title, description, CONCAT('gameinfo.php?id=', id) as url FROM game_info WHERE title LIKE ? OR description LIKE ?";
                $params[] = $searchQuery;
                $params[] = $searchQuery;
                $types .= "ss";
            }
            
            if (!empty($sql)) {
                $sql .= " LIMIT 10";
                $stmt = $db->prepare($sql);
                
                if ($stmt) {
                    if (!empty($params)) {
                        $stmt->bind_param($types, ...$params);
                    }
                    
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        $searchResults[] = [
                            'type' => $row['type'],
                            'title' => htmlspecialchars($row['title']),
                            'description' => htmlspecialchars(substr($row['description'], 0, 100) . '...'),
                            'url' => $row['url']
                        ];
                    }
                    
                    $stmt->close();
                }
            }
            
            $db->close();
        }
    }
} catch (Exception $e) {
    // Log the error but continue with static content
    error_log("Database error in search.php: " . $e->getMessage());
}

// Add static content search results
$staticContent = [
    [
        'type' => 'page',
        'title' => 'Game Library',
        'description' => 'Explore our collection of games and features',
        'url' => 'game.php#game-library'
    ],
    [
        'type' => 'page',
        'title' => 'Game Info',
        'description' => 'Information about Court Card Slash game',
        'url' => 'gameinfo.php'
    ],
    [
        'type' => 'page',
        'title' => 'Terms of Service',
        'description' => 'Read our terms of service and legal information',
        'url' => 'terms.php'
    ],
    [
        'type' => 'page',
        'title' => 'Privacy Policy',
        'description' => 'Learn about how we handle your data',
        'url' => 'privacy-policy.php'
    ],
    [
        'type' => 'page',
        'title' => 'FAQs',
        'description' => 'Frequently asked questions about our services',
        'url' => 'faqs.php'
    ],
    [
        'type' => 'page',
        'title' => 'How to Play',
        'description' => 'Learn how to play Court Card Slash',
        'url' => 'howtoplay.php'
    ],
    [
        'type' => 'page',
        'title' => 'Leaderboard',
        'description' => 'View top players and rankings',
        'url' => 'leaderboard.php'
    ],
    [
        'type' => 'page',
        'title' => 'Patch Notes',
        'description' => 'Latest updates and changes to the game',
        'url' => 'patchnotes.php'
    ],
    [
        'type' => 'page',
        'title' => 'Community Forum',
        'description' => 'Join discussions with other players',
        'url' => 'community-forum.php'
    ],
    [
        'type' => 'page',
        'title' => 'Creators',
        'description' => 'Meet the team behind Court Card Slash',
        'url' => 'creator.php'
    ]
];

// Filter static content based on search query
$queryLower = strtolower($query);
foreach ($staticContent as $content) {
    if (strpos(strtolower($content['title']), $queryLower) !== false || 
        strpos(strtolower($content['description']), $queryLower) !== false) {
        $searchResults[] = $content;
    }
}

// Sort results by relevance (exact matches first)
usort($searchResults, function($a, $b) use ($queryLower) {
    $aTitle = strtolower($a['title']);
    $bTitle = strtolower($b['title']);
    
    // Exact matches first
    $aExact = $aTitle === $queryLower;
    $bExact = $bTitle === $queryLower;
    
    if ($aExact && !$bExact) return -1;
    if (!$aExact && $bExact) return 1;
    
    // Starts with query next
    $aStarts = strpos($aTitle, $queryLower) === 0;
    $bStarts = strpos($bTitle, $queryLower) === 0;
    
    if ($aStarts && !$bStarts) return -1;
    if (!$aStarts && $bStarts) return 1;
    
    // Contains query last
    $aContains = strpos($aTitle, $queryLower) !== false;
    $bContains = strpos($bTitle, $queryLower) !== false;
    
    if ($aContains && !$bContains) return -1;
    if (!$aContains && $bContains) return 1;
    
    return 0;
});

// Debug output
error_log("Search results count: " . count($searchResults));
error_log("Search results: " . json_encode($searchResults));

echo json_encode($searchResults); 