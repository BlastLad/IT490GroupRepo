<?php
echo "packaged Validated or Fail? ";

$input = trim(fgets(STDIN)); // read user input from console

if ($input == "Validated") {
  echo "Package has been validated... now sending to prod\n";
} elseif ($input == "Fail") {
  echo "Package has failed... now sending back to Dev\n";
} else {
  echo "Invalid input\n";
}

?>
