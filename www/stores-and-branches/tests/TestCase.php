<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
  /**
   * Creates the application.
   *
   * @return \Laravel\Lumen\Application
   */
  public function createApplication()
  {
    return require __DIR__.'/../bootstrap/app.php';
  }

  public function seeHasHeader($header)
  {
    $this->assertTrue(
      $this->response->headers->has($header),
      "Response should have the header '{$header}' but does not."
    );
    return $this;
  }

  public function seeHeaderWithRegExp($header, $regexp)
  {
    $this->seeHasHeader($header)
      ->assertRegExp(
        $regexp,
        $this->response->headers->get($header)
      );
    return $this;
  }

  public function assertArrayEqual($ary1, $ary2, $fieldName)
  {
    $val1 = $this->getArrayFieldValues($ary1, $fieldName);
    $val2 = $this->getArrayFieldValues($ary2, $fieldName);

    $this->assertEmpty(array_diff($val2, $val1));
    $this->assertEmpty(array_diff($val1, $val2));
  }

  private function getArrayFieldValues($ary, $fieldName)
  {
    $vals = [];
    foreach($ary as $t){
      $vals[] = $t[$fieldName]?? $t->$fieldName;
    }

    return $vals;
  }
}
