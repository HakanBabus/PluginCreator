<?php

echo "Welcome plugin creator wizard. Please fill in the blanks or use defaults.\n";

$name = createQuestion("Plugin Name: ", "TemplatePlugin");
$author = createQuestion("Author: ", get_current_user());
$version = createQuestion("Plugin Version: ", "1.0.0");
$api_version = createQuestion("Api Version: ", "5.0.0");
$def = "$author".'\\' .str_replace(" ", "", $name)."\\".str_replace(" ", "", $name);
$main = createQuestion("Main File: ", $def);
$defaultmain = str_replace(" ", "", $name);
if($main !== $def){
    $defaultmain = $main;
    $main = "$author".'\\' .str_replace(" ", "", $name)."\\".str_replace(" ", "", $main);
}
$def = __DIR__;
if(is_dir($def."/plugins")){
    $def = __DIR__."/plugins";
}
$server_directory = createQuestion("Plugins Directory: ", $def);

if(!is_dir($server_directory)){
    error("Server plugins directory is not exists");
}

if(is_dir($server_directory."/$name")){
    error("Plugin is before created. Please delete or remove other directory.");
}

$pluginDirectory = $server_directory."\\$name\\";
mkdir($pluginDirectory);

echo "[I] Creating plugin.yml...\n";

$stream = fopen("$pluginDirectory\plugin.yml", "w");
$pluginyml = <<<END
name: "$name"
author: "$author"
version: "$version"
api: "$api_version"
main: $main
END;
fwrite($stream, $pluginyml);
fclose($stream);
echo "[I] Created plugin.yml.\n";

echo "[I] Creating directories and main file...\n";
$namespace = $author."\\".str_replace(" ", "", $name);
$mainDirectory = "$pluginDirectory\src\\$main.php";
mkdir("$pluginDirectory\src\\$namespace", 0777, true);
$stream = fopen($mainDirectory, "w");
$className = str_replace(" ", "", $name);
$c = '$this->getLogger()->info("Plugin ACTIVE -> '.$className.'")';
$mainPhp = <<<END
<?php

declare(strict_types=1);

namespace $namespace;

use pocketmine\plugin\PluginBase;

class $defaultmain extends PluginBase{

    public function onEnable(): void
    {
        $c;
    }
    
    public function onDisable(): void
    {
    
    }

}

END;

fwrite($stream, $mainPhp);
fclose($stream);
echo "[I] Created directories and main file.\n";
echo "[I] SUCCESSFULLY! \n[I] Created your plugin.\n";
echo "[N] Script author: https://github.com/HakanBabus";
exit();

function error(string $str){
    echo "[ERROR] ".$str;
    exit();
}

function createQuestion(string $str, mixed $default): mixed
{
    echo "[?] ".$str." ($default)\n> ";
    $i = readline();
    if(!$i or empty($i)){
        $i = $default;
    }
    echo "[S] Selected: $i\n-------------------------------\n";
    return $i;
}