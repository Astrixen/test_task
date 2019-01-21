# test_task

$searchObject = FSearch/FSearch::create($filename);

$result = $searchObject->search($needle); //array('line' => 0, 'position' => 0)

---

$config = new FSearch/FSearchConfig();

$config->setValue('maxSize', 1024);

$searchObject = FSearch/FSearch::create($filename, $config);
