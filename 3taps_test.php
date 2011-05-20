<?

DEFINE('API_KEY', 'jmrfhu59cnmtnzusshd62pbg');

require('3taps.php'); 
require('simpletest/autorun.php'); 


class TestOfSearchClient extends UnitTestCase {

	private $client;
	private $params = array(
			'rpp' => 4,
			'text' => 'honda',
			'source' => 'EBAYM' // eBay Motors
	);

	function __construct() {
		parent::__construct('3taps Search API test');

		$this->client = new threeTapsClient(API_KEY);
		$this->client->debug = true;
	}
	
	function testSearch() {
		$results = $this->client->search->search($this->params);
		$this->assertTrue($results['numResults'] > 0);
	}

	function testSummary() {
		$results = $this->client->search->summary($this->params);
		$this->assertTrue($results['totals'] > 0);
	}

	function testCount() {
		$result = $this->client->search->count($this->params);
		$this->assertTrue(count($result['count']) > 0);
	}

	function testRange() {
		$count_params = $this->params;
		$count_params['fields'] = 'price';
		$result = $this->client->search->range($count_params);
		$this->assertTrue(count($result['price']) > 0);
	}
}

class TestOfReferenceClient extends UnitTestCase {

	private $client;

	function __construct() {
		parent::__construct('3taps Reference API test');

		$this->client = new threeTapsClient(API_KEY);
		$this->client->debug = true;
	}
	
	function testCategory() {
		$results = $this->client->reference->category();
		$this->assertTrue(count($results) > 0);

		$results = $this->client->reference->category('VAUT');
		$this->assertTrue(count($results) > 0);
	}

	function testLocation() {
		$results = $this->client->reference->location();
		$this->assertTrue(count($results) > 0);
	}

	function testSource() {
		$results = $this->client->reference->source();
		$this->assertTrue(count($results) > 0);
	}
}

class TestOfGeocoderClient extends UnitTestCase {

	private $client;

	function __construct() {
		parent::__construct('3taps Geocoder API test');

		$this->client = new threeTapsClient(API_KEY);
		$this->client->debug = true;
	}
	
	function testGeocode() {
		$locations = array(
			array(
				'text' => '90210'
			)
		);
		$results = $this->client->geocoder->geocode($locations);
		$this->assertTrue(count($results) > 0);
	}
}

class TestOfStatusClient extends UnitTestCase {

	private $client;

	function __construct() {
		parent::__construct('3taps Status API test');

		$this->client = new threeTapsClient(API_KEY);
		$this->client->debug = true;
	}
	
	function testSystem() {
		$result = $this->client->status->system();
		$this->assertTrue($result['code'] == '200');
	}

	function testUpdate() {
		$postings = array(
			array(
				'status' => 'found',
				'externalID' => 'NOTANID',
				'source' => 'E_BAY'
			)
		);
		$result = $this->client->status->update($postings);
		$this->assertTrue($result['code'] == '200');
	}

	function testGet() {
		$postings = array(
			array(
				'externalID' => 'NOTANID',
				'source' => 'E_BAY'
			)
		);
		$results = $this->client->status->get($postings);
		$this->assertTrue(count($results));
		$this->assertTrue($results[0]['exists'] == true);
	}
}

class TestOfPostingClient extends UnitTestCase {

	private $client;

	private $postKeys = array();

	function __construct() {
		parent::__construct('3taps Posting API test');

		$this->client = new threeTapsClient(API_KEY);
		$this->client->debug = true;
	}
	
	function testCreate() {
		$postings = array(
			array(
				'source' => '3TAPS',
				'category' => 'VAUT',
				'heading' => 'This is a test posting.',
				'location' => 'LAX',
				'timestamp' => date('Y/m/d h:i:s')
			),
			array(
				'source' => '3TAPS',
				'category' => 'VAUT',
				'heading' => 'This is a test posting.',
				'location' => 'LAX',
				'timestamp' => date('Y/m/d h:i:s')
			)
		);

		$results = $this->client->posting->create($postings);
		$this->postKeys = array($results[0]['postKey'], $results[0]['postKey']);

		$this->assertTrue(count($results) == 2);
		$this->assertTrue($results[0]['postKey']);
		$this->assertTrue(!isset($results[0]['error']));
		$this->assertTrue($results[1]['postKey']);
		$this->assertTrue(!isset($results[1]['error']));
	}

	function testGet() {
		$result = $this->client->posting->get($this->postKeys[0]);
		$this->assertTrue($result);
	}

	function testUpdate() {
		$postings = array(
			array(
				$this->postKeys[0],
				array(
					'heading' => 'Updated Heading'
				)
			)
		);
		
		$result = $this->client->posting->update($postings);
		$this->assertTrue($result['success']);
	}

	function testDelete() {
		$result = $this->client->posting->delete($this->postKeys);
		$this->assertTrue($result['success']);
	}

	
}
