$sor = App\Models\Sor::find(6);
$item = App\Models\Item::find(141554);
$service = new App\Services\ItemSkeletonService();
$controller = new App\Http\Controllers\ItemSkeletonController($service);

echo "=== TESTING ITEM SKELETON CRUD ===\n\n";

echo "--- 1. Adding Resource ---\n";
$req1 = Illuminate\Http\Request::create('/api', 'POST', [
'resource_id' => 5000,
'quantity' => 2.5,
'unit_id' => 126
]);
$res1 = $controller->addResource($req1, $sor, $item);
echo $res1->getContent() . "\n";
$json1 = json_decode($res1->getContent());
$skelId = $json1->id ?? null;

echo "\n--- 2. Fetching Skeleton (after adding resource) ---\n";
$req2 = Illuminate\Http\Request::create('/api', 'GET');
$res2 = $controller->show($req2, $sor, $item);
$data = json_decode($res2->getContent(), true);
echo "Resource Count: " . count($data['resources']) . "\n";
echo "Sub-item Count: " . count($data['subitems']) . "\n";
echo "Overhead Count: " . count($data['overheads']) . "\n";
echo "Total Cost: " . ($data['totals']['grand_total'] ?? 'N/A') . "\n";
echo "Final Rate: " . ($data['totals']['final_rate'] ?? 'N/A') . "\n";

echo "\n--- 3. Adding Overhead ---\n";
$req3 = Illuminate\Http\Request::create('/api', 'POST', [
'overhead_id' => 1,
'parameter' => 10
]);
$res3 = $controller->addOverhead($req3, $sor, $item);
echo $res3->getContent() . "\n";
$json3 = json_decode($res3->getContent());
$oheadId = $json3->id ?? null;

echo "\n--- 4. Fetching Skeleton (after adding overhead) ---\n";
$res4 = $controller->show($req2, $sor, $item);
$data2 = json_decode($res4->getContent(), true);
echo "Overhead Count: " . count($data2['overheads']) . "\n";
echo "Total Cost: " . ($data2['totals']['grand_total'] ?? 'N/A') . "\n";

echo "\n--- 5. Removing Resource ---\n";
if($skelId) {
$skel = App\Models\Skeleton::find($skelId);
$res5 = $controller->removeResource($sor, $item, $skel);
echo $res5->getContent() . "\n";
}

echo "\n--- 6. Removing Overhead ---\n";
if($oheadId) {
$ohead = App\Models\Ohead::find($oheadId);
$res6 = $controller->removeOverhead($sor, $item, $ohead);
echo $res6->getContent() . "\n";
}

echo "\n--- 7. Final Skeleton State ---\n";
$res7 = $controller->show($req2, $sor, $item);
$data3 = json_decode($res7->getContent(), true);
echo "Resource Count: " . count($data3['resources']) . "\n";
echo "Overhead Count: " . count($data3['overheads']) . "\n";

echo "\n=== VERIFICATION COMPLETE ===\n";