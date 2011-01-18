<?php

namespace li3_junit\extensions\command;

use \lithium\test\Dispatcher;

class Junit extends \lithium\console\Command {

  public $case = null;

  public $group = null;

  public function help () {
    $message = "Usage: li3 junit --case=CASE|group=GROUP";
    $this->out($message);
    return true;
  }

  public function run() {
    $run = $this->case ?: $this->group;
    if ($run == null) {
      $this->help();
      return;
    }
    $run = '\\' . str_replace('.', '\\', $run);

    $report = Dispatcher::run($run, array(
      'reporter' => 'console',
      'format' => 'xml'
    ));

    $this->out($report->render('stats'));
  }
}

