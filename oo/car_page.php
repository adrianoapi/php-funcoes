<?php require '../_config.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Defining and Using Object Properties</title>
  </head>
  <body>
    <h1>Defining and Using Object Properties</h1>

<?php
 
    class Car {
      public $color;
      public $manufacturer;
    }

    $beetle = new Car();
    $beetle->color        = "red";
    $beetle->manufacturer = "Volkswagen";

    $mustang = new Car();
    $mustang->color        = "green";
    $mustang->manufacturer = "Ford";

    echo "<h2>Some properties:</h2>";
    echo "<p>The Beetle's color is " . $beetle->color . ".</p>";
    echo "<p>The Mustang's manufacturer is " . $mustang->manufacturer . ".</p>";
    echo "<h2>The \$beetle Object:</h2>";
    debug( $beetle );
    echo "<h2>The \$mustang Object:</h2>";
    debug( $mustang );
 
?>
  </body>
</html>