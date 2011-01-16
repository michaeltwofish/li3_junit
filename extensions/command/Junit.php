<?php

namespace li3_junit\extensions\command;

use lithium\test\Dispatcher;

class Junit extends \lithium\console\Command {

  public $case = null;

  public $group = null;

  public function run() {
    $run = $this->case ?: $this->group;
    $run = '\\' . str_replace('.', '\\', $run);

    $report = Dispatcher::run($run, array(
      'reporter' => 'console',
      'format' => 'xml'
    ));

    $this->out($report->render('stats'));
  }
}

