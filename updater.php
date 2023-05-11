<?php

include("tmpl/head.html");

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        throw new InvalidArgumentException("$dir must be a directory");
    }

    // Open the directory and iterate over its contents
    $files = scandir($dir);
    foreach ($files as $file) {
        // Skip "." and ".." directories
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            // Recursively delete subdirectories
            deleteDirectory($path);
        } else {
            // Delete files
            unlink($path);
        }
    }

    // Delete the directory itself
    rmdir($dir);
}

deleteDirectory(__DIR__.DIRECTORY_SEPARATOR."update_cache");
mkdir(__DIR__.DIRECTORY_SEPARATOR."update_cache");

$api_url = 'https://api.github.com/repos/abeiro/saig-gwserver/releases/latest';
$options = [
    'http' => [
        'header' => [
            'User-Agent: PHP'
        ]
    ]
];

$context = stream_context_create($options);
$data = file_get_contents($api_url, false, $context);
$release = json_decode($data);

$zipball_url = basename($release->zipball_url);
$url = "https://github.com/abeiro/saig-gwserver/archive/refs/tags/$zipball_url.zip";

$zipball_file = __DIR__.DIRECTORY_SEPARATOR.'update_cache/release.zip';
file_put_contents($zipball_file, fopen($url, 'r'));

$zip = new ZipArchive;
if ($zip->open($zipball_file) === TRUE) {
    $extract_path = 'update_cache/';
    $zip->extractTo($extract_path);
    $zip->close();

    
    
$orig_dir = __DIR__;
$update_dir = 'update_cache';

// Loop through files in update directory
echo "<ul>";
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($update_dir)) as $file) {
    if ($file->isFile()) {
        $relative_path = str_replace($update_dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $relative_path=str_replace("saig-gwserver-{$zipball_url}",'',$relative_path);
        
        $orig_file = $orig_dir . DIRECTORY_SEPARATOR . $relative_path;
        //$newPath = basename(dirname($orig_file)) . '/' . basename($orig_file);
        //$orig_file=$newPath;
        
        // Check if file exists in orig directory
        if (file_exists($orig_file)) {
            // Compare MD5 hash of files
            $update_md5 = md5_file($file->getPathname());
            $orig_md5 = md5_file($orig_file);

            if ($update_md5 === $orig_md5) {
                echo "<li>File $relative_path has not changed  ($orig_file vs {$file->getPathname()})</li>";
            } else {
                echo "<li><b>File $relative_path has changed</b> ($orig_file vs {$file->getPathname()})\n</li>";
                if ($_POST["doit"]) {
                  echo "updating..";
                  copy($file->getPathname(),$orig_file);
                }
                // Do something to update the file
            }
        } else {
            echo "<li><span style='color:red'>File $relative_path is a new file</b>  ($orig_file vs {$file->getPathname()})</span></li>";
             if ($_POST["doit"]) {
                  echo "updating..";
                  @mkdir(dirname($orig_file));
                  copy($file->getPathname(),$orig_file);
                }
                
            // Do something to add the file
        }
    }
}
echo "</ul>";
echo "
<p><strong>Note. Files marked in bold, will be ovewritten!!!!</strong>
<form action='updater.php' method='post'>
    ".(($_POST["doit"])?"<input type='submit' value='Proceed' name='doit' onclick=\"return confirm('Are you sure?')\">":"")."
    <input type='button' value='Back' onclick=\"location.href='index.php'\"/>
    <input type='button' value='Refresh' onclick=\"location.href='updater.php'\"/>
</form>



";
    
} else {
    echo "Failed to open ZIP archive\n";
}


?>
