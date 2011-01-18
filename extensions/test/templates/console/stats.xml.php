<?php
	use \DOMDocument;

	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$testsuites = $dom->createElement('testsuites');
	if ($self->title) {
		$testsuites->setAttribute('name', $self->title);
	}

	// Grab the totals and add attributes to the testsuite
	$stats = $self->stats();
	$count = $stats['count'];
	$attrs = array(
		'tests' => intval($count['asserts']) ?: 0,
		'failures' => intval($count['fails']) ?: 0,
		'errors' => intval($count['exceptions']) ?: 0,
	);

	foreach ($attrs as $attr => $value) {
		$testsuites->setAttribute($attr, $value);
	}

	// Iterate over the test results
	foreach ($self->results['group'] as $group) {
		$testsuite = $dom->createElement('testsuite');
		foreach ($group as $result) {
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
		$testsuites->appendChild($testsuite);
	}

	$dom->appendChild($testsuites);

	echo $dom->saveXML();
?>
