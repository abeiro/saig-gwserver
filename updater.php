<?php

include("tmpl/head.html");
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

    
    
    $orig_dir = '.';
$update_dir = 'update_cache';

// Loop through files in update directory
echo "<ul>";
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($update_dir)) as $file) {
    if ($file->isFile()) {
        $relative_path = str_replace($update_dir . '/', '', $file->getPathname());
        $relative_path=str_replace("saig-gwserver-{$zipball_url}",'',$relative_path);
        
        $orig_file = $orig_dir . '/' . $relative_path;
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
            echo "<li>File $relative_path is a new file</b>  ($orig_file vs {$file->getPathname()})</li>";
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
    <input type='submit' value='Proceed' name='doit' onclick=\"return confirm('Are you sure?')\">
    <input type='button' value='Back' onclick=\"location.href='index.php'\"/>
    <input type='button' value='Refresh' onclick=\"location.href='updater.php'\"/>
</form>



";
    
} else {
    echo "Failed to open ZIP archive\n";
}


?>
