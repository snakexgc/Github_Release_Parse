<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get GitHub project URL from form data
    $github_url = $_POST["github_url"];

    // Function to get latest release information from GitHub API
    function get_latest_release($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
        ]);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status_code != 200) {
            return null;
        }

        return json_decode($response, true);
    }

    // Function to get download URLs for all assets in a release
    function get_download_urls($release_url, $accelerate) {
        $ch = curl_init($release_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $release_data = json_decode($response, true);

        if (!isset($release_data['assets'])) {
            return null;
        }

        $download_urls = array();
        foreach ($release_data['assets'] as $asset) {
            $download_url = $asset['browser_download_url'];
            if ($accelerate) {
                $download_url = 'https://git.20010101.xyz/' . $download_url;
            }
            $download_urls[] = $download_url;
        }
        return $download_urls;
    }

    // Parse GitHub project URL to get repository owner and name
    $parsed_url = parse_url($github_url);
    if ($parsed_url === false || !isset($parsed_url['host']) || $parsed_url['host'] !== 'github.com') {
        die("Error: 请输入一个Github仓库链接！");
    }

    $path = explode('/', trim($parsed_url['path'], '/'));
    if (count($path) < 2) {
        die("Error: 请输入一个合规的Github仓库链接！");
    }

    $owner = $path[0];
    $repo = $path[1];

    // Construct GitHub API URLs for latest release and pre-release
    $latest_release_url = "https://api.github.com/repos/$owner/$repo/releases/latest";
    $pre_release_url = "https://api.github.com/repos/$owner/$repo/releases";

    // Get latest release information
    $latest_release = get_latest_release($latest_release_url);

    // Get pre-release information
    $pre_release = get_latest_release($pre_release_url);

    // Check if latest release or pre-release is available
    $latest_available = ($latest_release !== null);
    $pre_release_available = ($pre_release !== null);

    // Check if pre-release exists by comparing latest and pre-release versions
    if ($latest_release !== null && $pre_release !== null && isset($latest_release['tag_name']) && isset($pre_release['tag_name'])) {
        $pre_release_available = ($latest_release['tag_name'] !== $pre_release['tag_name']);
    }

    if (!$latest_available && !$pre_release_available) {
        die("Error: 未能获取到信息，可能是服务器IP进入风控状态，请稍后再试！");
    }

    // Check if acceleration is enabled
    $accelerate = isset($_POST['accelerate']) && $_POST['accelerate'] == 'on';

    // Get download URLs for latest release and pre-release
    $latest_download_urls = $latest_available ? get_download_urls($latest_release['url'], $accelerate) : null;
    $pre_release_download_urls = $pre_release_available ? get_download_urls($pre_release[0]['url'], $accelerate) : null;

    if($latest_release['tag_name'] === $pre_release[0]['tag_name']){
        $pre_release_available = false;
    }

    // Display latest release and pre-release information
    if ($latest_available || $pre_release_available) {
        echo "<h2>Latest Release:</h2>";
    }

    if ($latest_available) {
        echo "<p>Version: " . $latest_release['tag_name'] . "</p>";
        echo "<p>Release Notes: " . $latest_release['body'] . "</p>";
        echo "<p>Downloads:</p>";
        if ($latest_download_urls !== null) {
            foreach ($latest_download_urls as $url) {
                echo "<a href='$url'>$url</a><br>";
            }
        } else {
            echo "<p>No download available.</p>";
        }
    } elseif ($pre_release_available) {
        echo "<p>No latest release available.</p>";
    }
    
    if ($pre_release_available) {
        echo "<h2>Latest Pre-release:</h2>";
        echo "<p>Version: " . $pre_release[0]['tag_name'] . "</p>";
        echo "<p>Release Notes: " . $pre_release[0]['body'] . "</p>";
        echo "<p>Downloads:</p>";
        if ($pre_release_download_urls !== null) {
            foreach ($pre_release_download_urls as $url) {
                echo "<a href='$url'>$url</a><br>";
            }
        } else {
            echo "<p>No download available.</p>";
        }
    } elseif ($latest_available) {
        echo "<h2>Latest Pre-release:</h2>";
        echo "<p>No pre-release available.</p>";
    }
}

?>
