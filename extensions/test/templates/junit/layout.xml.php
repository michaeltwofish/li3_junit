<?php
	use \lithium\util\Inflector;
	use \DOMDocument;
	$base = $request->env('base');
	$stats = $report->stats();

	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$testsuite = $dom->createElement('testsuite');
	if ($report->title) {
		$testsuite->setAttribute('name', $report->title);
	}

	// Grab the totals and add attributes to the testsuite
	$count = $stats['count'];
	$attrs = array(
		'asserts' => intval($count['asserts']) ?: 0,
		'fails' => intval($count['fails']) ?: 0,
		'exceptions' => intval($count['exceptions']) ?: 0,
	);

	foreach ($attrs as $attr => $value) {
		$testsuite->setAttribute($attr, $value);
	}

	// Iterate over the test results
	// @todo Why 'group' an array of tests? Other tests?
	foreach ($report->results['group'][0] as $result) {
		$testcase = $dom->createElement('testcase');
		$testcase->setAttribute('classname', $result['class']);
		$testcase->setAttribute('name', $result['method']);
		if ($result['result'] == 'fail' || $result['result'] == 'exception') {
			$error_element = null;
			$element = '';
			$message = "{$result['class']}::{$result['method']}()\n";
			$message .= "{$result['message']}\n";
			if ($result['result'] == 'fail') {
				$element = 'failure';
				$type = 'failed';
				$message .= "Expected {$result['data']['expected']} but got {$result['data']['result']}\n";
				$message .= "{$result['file']}:{$result['line']}\n";
			} elseif ($result['result'] == 'exception') {
				$element = 'error';
				$type = 'Exception';
				$message .= "{$result['file']}:{$result['line']}\n";
				if (isset($result['trace']) && !empty($result['trace'])) {
					$message .= "Trace: {$result['trace']}\n";
				}
			}
			$error_element = $dom->createElement($element, $message);
			$error_element->setAttribute('type',$type);
			$testcase->appendChild($error_element);
		}
		$testsuite->appendChild($testcase);
	}

	$dom->appendChild($testsuite);

	echo $dom->saveXML();
?>
