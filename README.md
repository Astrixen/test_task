# test_task

$searchObject = FSearch/FSearch::create($filename);

$result = $searchObject->search($needle);

---

$config = new FSearch/FSearchConfig();

$config->setValue('maxSize', 1024);

$searchObject = FSearch/FSearch::create($filename, $config);
