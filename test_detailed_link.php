<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Detailed Statement Link ===\n\n";

// Test the route exists
$routeCollection = app('router')->getRoutes();
$detailedRoute = null;

foreach ($routeCollection as $route) {
    if ($route->getName() === 'reports.detailed-statement') {
        $detailedRoute = $route;
        break;
    }
}

if ($detailedRoute) {
    echo "✅ Route 'reports.detailed-statement' found\n";
    echo "   URI: " . $detailedRoute->uri() . "\n";
    echo "   Methods: " . implode(', ', $detailedRoute->methods()) . "\n";
    echo "   Controller: " . $detailedRoute->getAction('uses') . "\n";
} else {
    echo "❌ Route 'reports.detailed-statement' not found\n";
}

// Test if the view file exists
$viewPath = resource_path('views/reports/detailed-statement.blade.php');
if (file_exists($viewPath)) {
    echo "✅ View file exists: detailed-statement.blade.php\n";
} else {
    echo "❌ View file missing: detailed-statement.blade.php\n";
}

// Test the controller method
try {
    $controller = new \App\Http\Controllers\ReportController();
    $request = new \Illuminate\Http\Request();
    
    echo "✅ Controller can be instantiated\n";
    
    // Test if method exists
    if (method_exists($controller, 'detailedStatement')) {
        echo "✅ Method 'detailedStatement' exists in controller\n";
    } else {
        echo "❌ Method 'detailedStatement' missing in controller\n";
    }
    
} catch (Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing URL Generation ===\n";
try {
    $url = route('reports.detailed-statement');
    echo "✅ URL generated: " . $url . "\n";
} catch (Exception $e) {
    echo "❌ URL generation failed: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "The 'View Detailed Statement' button should now work correctly.\n";
echo "It links to: " . route('reports.detailed-statement') . "\n";
echo "Make sure you are logged in to access the reports.\n";
