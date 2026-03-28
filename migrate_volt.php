<?php

$viewsDir = __DIR__ . '/resources/views/livewire';
$appDir = __DIR__ . '/app/Livewire';

if (!is_dir($appDir)) {
    mkdir($appDir, 0755, true);
}

if (file_exists($viewsDir . '/dashboard/dashboard.blade.php') && file_exists($viewsDir . '/dashboard/dashboard.php')) {
    $blade = file_get_contents($viewsDir . '/dashboard/dashboard.blade.php');
    $php = file_get_contents($viewsDir . '/dashboard/dashboard.php');
    file_put_contents($viewsDir . '/dashboard.blade.php', "<?php\n\n" . $php . "\n?".">\n" . $blade);
    unlink($viewsDir . '/dashboard/dashboard.blade.php');
    unlink($viewsDir . '/dashboard/dashboard.php');
    rmdir($viewsDir . '/dashboard');
}

$files = glob($viewsDir . '/*.blade.php');

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $start = strpos($content, '<?php');
    $end = strpos($content, '?>');
    
    if ($start !== false && $end !== false) {
        $phpBlock = substr($content, $start + 5, $end - ($start + 5));
        
        if (strpos($phpBlock, 'class extends Component') === false) {
            continue;
        }
        
        $fullBlock = substr($content, $start, $end + 2 - $start);
        $bladeContent = str_replace($fullBlock, '', $content);
        $bladeContent = ltrim($bladeContent);
        
        $basename = basename($file, '.blade.php');
        $segments = explode('-', $basename);
        $segments = array_map('ucfirst', $segments);
        $className = implode('', $segments);
        
        $phpBlock = str_replace('use Livewire\Volt\Component;', "use Livewire\Component;\nuse Illuminate\View\View;", $phpBlock);
        
        $phpBlock = preg_replace_callback('/new\s+(#\[Title\([^\)]+\)\]\s+)?class\s+extends\s+Component(.*?)(\s*\{)/is', function ($m) use ($className) {
            $attribute = trim($m[1] ?? '');
            $implements = trim($m[2] ?? '');
            
            $result = "";
            if ($attribute) {
                $result .= $attribute . "\n";
            }
            $result .= "class $className extends Component";
            if ($implements) {
                $result .= " " . $implements;
            }
            $result .= "\n{";
            return $result;
        }, $phpBlock);

        $renderMethod = "\n\n    public function render(): View\n    {\n        return view('livewire." . $basename . "');\n    }\n}";
        $phpBlock = preg_replace('/};\s*$/', $renderMethod, trim($phpBlock));
        
        $classContent = "<?php\n\nnamespace App\\Livewire;\n\n" . $phpBlock . "\n";
        
        file_put_contents($appDir . '/' . $className . '.php', $classContent);
        file_put_contents($file, $bladeContent);
        
        echo "Migrated $basename to App\\Livewire\\$className\n";
    }
}
