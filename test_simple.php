use App\Services\CctvService;

$service = new CctvService();
echo "Testing CCTV Service connection...\n";

$status = $service->getServiceStatus();
echo "Status: " . $status['status'] . "\n";

if (isset($status['error'])) {
    echo "Error: " . $status['error'] . "\n";
}

if (isset($status['response_time'])) {
    echo "Response time: " . number_format($status['response_time'] * 1000, 2) . "ms\n";
}

echo "Configuration:\n";
echo "Base URL: " . config('cctv.service.base_url') . "\n";
echo "Timeout: " . config('cctv.service.timeout') . "s\n";
