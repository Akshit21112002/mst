<!DOCTYPE html>
<html>
<head>
<title>
Experiment</title>
<style>
html, body {
    height: 100%;
    margin: 0;
    overflow-x: hidden;
    overflow-y: auto;
    padding-bottom: 2px; /* keep tiny bottom padding to avoid clipping */
}
table, tr, th, td {
    border:1px solid black;
    border-collapse: collapse;
    width:absolute;
    height:absolute;
}
h1 {
    text-align: center;
}

h2{
    text-align: center;
}

/* Tighten top margins to give more space to the maze */
h3 { margin: 4px 0 2px 0; }
#ex1_container p { margin: 2px 0; }

#ex1_container { 
    align:center; 
    text-align: center;
}

.maze-container {
    width: 98vw;
    height: 80vh; /* Reserve space for progress & text */
    overflow: hidden; /* 🔴 Critical: Disable scroll */
    margin: 0 auto;
    margin-top: 10px;
    border: 2px solid #ccc;
    border-radius: 5px;
    background: #f9f9f9;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}

.maze-table {
    margin: 0 auto;
    border-spacing: 0;
    border-collapse: collapse;
    table-layout: fixed;
}

.maze-table td {
    padding: 0;
    margin: 0;
    border: 0 none;
    line-height: 0;
}

/* Responsive design for very large maps */


@media (max-width: 900px) {
    .maze-container {
        max-width: 98vw;
        max-height: none; /* do not constrain height on small screens */
    }
}

</style>
</head>
<body onload="loadEventHandler()">

<?php

// form parametres
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

// define variables and set to empty values
$UID =  "";
$dir = "att-test";
$steps = 0; 
$practice_dir = "webfile/practice";
$easy_dir="webfile/easy";
$med_dir="webfile/medium";
$hard_dir="webfile/hard";

$practice_names = scandir($practice_dir);
$med_names = scandir($med_dir);
$easy_names = scandir($easy_dir);
$hard_names = scandir($hard_dir);
$number_of_practice = count($practice_names) - 2;
$num_easy = count($easy_names) - 2;
$num_med = count($med_names) - 2; // 0
$num_hard = count($hard_names) - 2; // 0
$num_test = $num_easy + $num_med + $num_hard; //i + $num_med + $num_hard;

// echo "number_of_practice  $number_of_practice <br>";
// echo "num_easy  $num_easy <br>";
// echo "num_med  $num_med <br>";
// echo "num_hard  $num_hard <br>";

$ip=$_SERVER['REMOTE_ADDR'];
$date = date('d/F/Y h:i:s'); // date of the visit that will be formated this way: 21/May/2011 2512:20:03
$browser = $_SERVER['HTTP_USER_AGENT'];
$browser = str_replace(' ', '_', $browser);
$validrequest = 0;

$randomisedMazeNo = 0; 
$mazeno = 0;
$maxDisplayWidth = 0.95 * 1920; // 95vw, adjust if your screen is smaller
$maxDisplayHeight = 0.8 * 1080; // 80vh, adjust if your screen is smaller

if ($validrequest == 1) {
    $temp_file = fopen($mfile, "r") or die("Unable to open file!" . $mfile);
    $temp_width = intval(fgets($temp_file));
    $temp_height = intval(fgets($temp_file));
    fclose($temp_file);

    $cellSizeX = $maxDisplayWidth / $temp_width;
    $cellSizeY = $maxDisplayHeight / $temp_height;
    $cellsize = min($cellSizeX, $cellSizeY);
    $cellsize = max($cellsize, 5); // Minimum 5px for usability
} else {
    $cellsize = 40;
}
if (!is_writable($dir)) {
    echo 'The directory is not writable ' . $dir . '<br>';
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $UID = "dodgyuser";
        $s = $dir . "/" . $UID . ".txt";
        $f = fopen($s, "a") or die("GET request received. Unable to open file!" . $s);
	fwrite($f, $ip . "\t". $date . "\t" . $browser . "Invalid request to test.php\n");
        fclose($f);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $validrequest = 1;
   $mazeno = 0;

   if (!empty($_POST["UID"])) {
      $UID = test_input($_POST["UID"]);
      if ( !strcmp($UID, "dodgyuser") ) {
        $validrequest = 0;
      }

     // echo $UID;
   } else {
      $validrequest = 0;
      $UID = "dodgyuser";
   }
 
   if (!empty($_POST["quizAnswer"])) {
      $quiz1 = test_input($_POST["quizAnswer"]);
   } 

   if (!empty($_POST["firsttrial"])) {
      $firsttrial = test_input($_POST["firsttrial"]);
   }


   $s = $dir . "/" . $UID . ".txt";
   $f = fopen($s, "a") or die("101 Unable to open file!" . $s);

   if(!empty($firsttrial)) {

      $m_easy = array();
      for ($x = 0; $x < $num_easy; $x++) {
         $m_easy[] = $x;
      }

      // permute randomly
      for ($x = 0; $x < count($m_easy); $x++) {
        $pickone = rand(0, count($m_easy)-1);
        if ($pickone <> $x) { 
    		$temp = $m_easy[$x];
        	$m_easy[$x] = $m_easy[$pickone];
        	$m_easy[$pickone] = $temp;
        }
      } 

      // XXX ESSIE 
      // -----------------------------------------
      $m_hard = array();
      for ($x = 0; $x < $num_hard; $x++) {
        $m_hard[] = $x;
      }

      // permute randomly
      for ($x = 0; $x < count($m_hard); $x++) {
        $pickone = rand(0, count($m_hard)-1);
        if ($pickone <> $x) { 
    		$temp = $m_hard[$x];
        	$m_hard[$x] = $m_hard[$pickone];
        	$m_hard[$pickone] = $temp;
        }
      } 

      $m_med = array();
      for ($x = 0; $x < $num_med; $x++) {
        $m_med[] = $x;
      }

      // permute randomly
      for ($x = 0; $x < count($m_med); $x++) {
        $pickone = rand(0, count($m_med)-1);
        if ($pickone <> $x) { 
    		$temp = $m_med[$x];
        	$m_med[$x] = $m_med[$pickone];
        	$m_med[$pickone] = $temp;
        }
      } 

      $m = array_merge($m_easy, $m_med, $m_hard);
      // -----------------------------------------

      // save to file

      $f = fopen($dir . "/" . $UID . "sequence.txt", "a") or die("Unable to open file!" . $UID . "sequence");
      for ($x = 0; $x < count($m); $x++) {
         fwrite($f, $m[$x] . "\n");
      }
      fclose($f);
      $randomisedMazeNo = 0;

   }
   else if (!empty($quiz1)) {
        $txt = $ip . " " . $date . " " . $browser .  " " . $UID .  " solvingquiz: " . $quiz1 .  "\n";
        fwrite($f, $txt);
        fclose($f);
        $quizcorrect = test_input($_POST["quizcorrect"]);
        //echo "quiz is correct" . $quizcorrect;
        if ( !strcmp($quizcorrect, "no") ) { 
          $mazeno = 0;
        } else {
          $mazeno = $number_of_practice-1;
          $randomisedMazeNo = $mazeno;
          advanceMazeNo();
        }
   } 
   else {
        if (!empty($_POST["mazeno"])) {
          $mazeno = test_input($_POST["mazeno"]);
          $randomisedMazeNo = $mazeno;
        } 
        else {
          $mazeno = 0;
          $randomisedMazeNo = 0;
          $mfile = $practice_dir . "/" . $practice_names[$randomisedMazeNo + 2 ];//$mazefile[$randomisedMazeNo];
          $txt = "1 of " . $number_of_practice;
        } 
        
        $mazeid = test_input($_POST["mazeID"]);
        $path = test_input($_POST["path"]);
        $time = test_input($_POST["time"]);
        $maze = test_input($_POST["name"]);
        $steps = test_input($_POST["steps"]);
   
        fwrite($f, $ip . " ". $date . " " . $browser . " " . $UID . " " . $maze . " " . $steps . " " . $path . " " . $time . "\n");
        fclose($f);

        // echo "??? randomisedMazeNo  $randomisedMazeNo <br>";
        advanceMazeNo();
   }
}

// agent location
$agent_x = 0;
$agent_y = 0;

if ($mazeno >=  $number_of_practice  + $num_easy + $num_med  && $mazeno <  $number_of_practice + $num_test ) {
        // print_r($hard_names) ;
        // echo "<br><br><h3 align='center'> hard mazes: index = " . $randomisedMazeNo . ", maze name = " .  $hard_names[$randomisedMazeNo + 2 ] . "</h3>";
        $mfile = $hard_dir . "/" . $hard_names[$randomisedMazeNo + 2];//$mazefile[$randomisedMazeNo];
} 
else if ($mazeno >=  $number_of_practice + $num_easy && $mazeno <  $number_of_practice + $num_easy + $num_med ) {
        // print_r($med_names) ;
        // echo "<br><br><h3 align='center'> medium mazes: index = " . $randomisedMazeNo . " ,maze name = " .  $med_names[$randomisedMazeNo + 2 ] . "</h3>";
        $mfile = $med_dir . "/" . $med_names[$randomisedMazeNo + 2];//$mazefile[$randomisedMazeNo];
}
else if ($mazeno >=  $number_of_practice && $mazeno <  $number_of_practice + $num_easy ) {
        // print_r($easy_names) ;
        // echo "<br><br><h3 align='center'> easy mazes: index = " . $randomisedMazeNo . ", maze name = " .  $easy_names[$randomisedMazeNo + 2 ] . "</h3>";
        $mfile = $easy_dir . "/" . $easy_names[$randomisedMazeNo + 2];//$mazefile[$randomisedMazeNo];
} 
else {
        // print_r($practice_names);
        // echo "<br><br><h3 align='center'> practice mazes: index = " . $randomisedMazeNo . ", maze name = " .  $practice_names[$randomisedMazeNo + 2 ] . "</h3>";
        $mfile = $practice_dir . "/" . $practice_names[$randomisedMazeNo + 2 ];//$mazefile[$randomisedMazeNo];
}

if ($validrequest == 1) {

  if ($mazeno == 0) {
    echo "<h3 style='font-family: Optima' align='center'> Practice Maze 1 of " . $number_of_practice . "</h3>";
    echo "<p style='font-family: Optima; font-size: 17px' align='center'>";
    echo "Let's look at this map. There are some black squares, a brick wall, and your character.<br><br>";
    echo "There is ONE exit in this maze. This exit could be behind any one of the black cells.<br><br>";
    echo "You can move your blue character by clicking one of adjacent white cells or using arrow keys (↑↓←→) or WASD keys.</p>";
    echo "<p style='font-family: Optima; font-size: 17px' align='center'>Please find the exit in as <mark>few steps</mark> as possible.</p>";
  } 
  else if ($mazeno < $number_of_practice) { 
    $txt = "Practice Maze " . ($mazeno+1) . " of " . $number_of_practice;
    echo "<h3 style='font-family: Optima' align='center'>" . $txt . "</h3>";
    echo "<p  style='font-family: Optima; font-size: 17px' align='center'>Use mouse clicks or arrow keys (↑↓←→) / WASD to move. Please find the exit in as <mark>few steps</mark> as possible.</p>";
  } 
  else {
    $txt = "Maze " . ($mazeno- $number_of_practice +1) . " of " . $num_test;
    echo "<h3 style='font-family: Optima' align='center'>" . $txt . "</h3>";
    echo "<p style='font-family: Optima; font-size: 17px' align='center'>Use mouse clicks or arrow keys (↑↓←→) / WASD to move. Please find the exit in as <mark>few steps</mark> as possible.</p>"; 
  }
} else {
  echo "<h2>Err: Invalid Request</h2>\n";
}


function validatex() {
    return (validInput==1);
}

function advanceMazeNo() {
    global $mazeno, $randomisedMazeNo, $num_easy, $num_med, $num_hard, $UID, $dir,  $number_of_practice;
    $mazeno = $mazeno + 1;
    $randomisedMazeNo = $mazeno;

    // echo "randomisedMazeNo1  $randomisedMazeNo <br>";
    // echo "mazeno $mazeno, number_of_practice $number_of_practice <br>";

    // echo "HERE $mazeno, $number_of_practice, $temp, $num_easy, $num_med, $num_hard <br>";

    // echo "ID $UID <br>";
 
    // is this a practice or a real trial?
    if ($mazeno > $number_of_practice-1) {
         $s = $dir . "/" . $UID . "sequence.txt";
         $f = fopen($s, "r") or die("102: Unable to open file! " . $s);
        
         $temp = $mazeno- $number_of_practice; 
        //  echo "HERE2 $temp <br>";

        // ESSIE Not sure why the shifting was necessary; there shouldn't be any shifting
        //  if ($temp - $num_easy  >= 0){
        //   $temp = $temp - $num_easy;
        //  } 
        //  else if ($temp - $num_med >= 0){
        //   $temp = $temp - $num_med;
        //  } 
        //  else if ($temp - $num_hard >= 0){
        //   $temp = $temp - $num_hard;
        //  }
         
        //  echo "HERE3 $temp <br>";

         for ($x = 0; $x <= $temp; $x++) {
           $randomisedMazeNo = intval(fgets($f));
          //  echo "what is this doing $randomisedMazeNo <br>";
         }
         fclose($f);
   }

  // echo "advanced to $randomisedMazeNo <br>";
}

function readWorld($fname) {
        global $worldWidth, $worldHeight, $worldmap, $agent_x, $agent_y;
        $world = fopen( $fname, "r") or die("Unable to open file!" . $fname);
        $worldWidth = fgets($world);
        $worldHeight = fgets($world);
        $agent_x = 0; $agent_y=0;

        // create a world array
        $worldmap = array();

        for ($y = 0; $y < $worldHeight; $y++) {
                $mazeLine = fgets($world);
                $line = str_split($mazeLine);
                $worldmap[$y] = array(); 
                
                for ($x = 0; $x < $worldWidth; $x++) {
                        $worldmap[$y][$x] =  $line[$x];
                        
                        if ($worldmap[$y][$x] == 5) {
                           $agent_x = $x; $agent_y=$y;
                        }
                }
        }
        fclose($world);
}

// echo "<br>reading world $mfile<br>";

readWorld($mfile);

?>

<script>
var ax = <?php global $agent_x;  echo "$agent_x" ?>;
var ay = <?php global $agent_y;  echo "$agent_y" ?>;
var height = <?php global $worldHeight; echo "$worldHeight"?>;
var width = <?php global $worldWidth;  echo "$worldWidth"?>;
var warr = <?php global $worldmap; echo json_encode($worldmap); ?>;

var mn = <?php global $mazeno; echo $mazeno; ?>;
var mnr = <?php global $randomisedMazeNo; echo $randomisedMazeNo; ?>;
var mf = "<?php global $mfile;  echo  $mfile; ?>";
var u_id = "<?php global  $UID; echo  $UID; ?>";
var savedpath = "p(" + ax + "," + ay +  ")";//"p(0,0);";
var savedtime = "";
var valid = <?php global $validrequest;  echo $validrequest; ?>;
var num_test = <?php global $num_test;  echo $num_test; ?>;
var num_practice = <?php global $number_of_practice;  echo $number_of_practice; ?>;
var showEnd = "<?php global $showEndNext; echo $showEndNext; ?>";
var cellsize = "<?php global $cellsize; echo $cellsize; ?>";
var firsttrial = "<?php global $firsttrial; echo $firsttrial; ?>";
jssteps = <?php global $steps; echo $steps;?>;
var progress_step = 1;
var quizcorrect = "<?php global $quizcorrect; echo $quizcorrect; ?>";
var maxsteps = 500;
var oldtime = new Date();
var fitPasses = 0;

// this does work
// alert(u_id);

var seen = new Array(height);
var visible = new Array(height);

// allocate seen array
for (y = 0; y < height; y++) {
   seen[y] = new Array(width);
   visible[y] = new Array(width);
   for (x = 0; x < width; x++) {
        seen[y][x] = 0;
        visible[y][x] = 0;
        if (parseInt(warr[y][x]) == 6) {
           visible[y][x] = 1;
	}

        if (parseInt(warr[y][x]) == 5) {
           warr[y][x] = "0";
           visible[y][x] = 1;
        }	
   }
}

// 1 -- no rendered boundary, 2 -- treasure, 3 -- wall, 5 - agent starting location, 0 - empty, 6 - open

calculate_seen();


    function timestamp() {
        var now= new Date(), 
            h= now.getHours(), 
            m= now.getMinutes(), 
            s= now.getSeconds();
        ms = now.getMilliseconds();
        return ms + 1000*s + 1000*60*m + 1000*60*60*h;
    }
 
function calculate_seen() {
  for (y = 0; y < height; y++) {
    for (x = 0; x < width; x++) {
        if (( Math.abs(ax-x) + Math.abs(ay-y)) <= 1){  
          seen[y][x] = 1;
        }
    } 
  }

  for (i = 10; i > 0; i--) {
      isovistLevel(i);
  }

  for (y = 0; y < height; y++) {
    for (x = 0; x < width; x++) {
        if (visible[y][x] == 1 )  {
           seen[y][x] = 1;
        }
    }
  } 
}

function isovistLevel(level) {
    for (px = ax-level; px <= ax+level; px++) {
      addvisible(px, ay-level, level); // top row
      addvisible(px, ay+level, level); // bottom row
    }

    for (py = ay-level; py < ay+level; py++) {
      addvisible(ax+level, py, level);  // right row
      addvisible(ax-level, py, level);  // left row
    }
}

function addvisible(px, py, level) {
    if (px >= 0 && py >=  0 &&  px < width &&  py < height) {
        visible[py][px] = 0;
        var b = true;
        if (level > 1 || (Math.abs(ax-px) + Math.abs(ay-py) > 1) ) {
                if (b) b = checkline(ax+0.1, ay+0.1, px+0.1, py+0.1, level*45.0);
                if (b) b = checkline(ax+0.9, ay+0.1, px+0.9, py+0.1, level*45.0);
                if (b) b = checkline(ax+0.9, ay+0.9, px+0.9, py+0.9, level*45.0);
                if (b) b = checkline(ax+0.1, ay+0.9, px+0.1, py+0.9, level*45.0);
                if (b) visible[py][px] = 1;
        }
        if (b) visible[py][px] = 1; 
    }
}

function checkline(x1, y1, px, py, step) {
    var dx = (x1-px)/step;
    var dy = (y1-py)/step;

    var fx = px+dx;
    var fy = py+dy;
    var x = Math.floor(fx);
    var y = Math.floor(fy);

    do {
      if (px < 0 || py < 0 || px >= width || py >= height ) {
        return true;
      }

      var w = parseInt(warr[y][x]);
      if (w == 3) return false;

      fx +=dx; 
      fy +=dy;      
      x = Math.floor(fx);  
      y = Math.floor(fy);
    } while (x !=Math.floor(x1) || y !=Math.floor(y1));
    return true;
}




function generate_table(wagent) {
    // generate progress
     var bigtable = "<p><table align='center' style='widthoat:center; border:0px solid white;' cellpadding='10' > <tr style=' border:none'>";

    healthbar = " <table align='center' style='width:absolute;widthoat:center'>";
    bar = "";
    
    for (x = 0; x < maxsteps; x++) {
        cl = "#00a055";
        if (progress_step > 25) {
          cl = "#f90000";
        } else
        if (progress_step > 22) {
          cl = "#ff0000";
        } else if (progress_step > 19) {
          cl = "#ff5500";
        } else if (progress_step > 16) {
          cl = "#ff9a33";
        } else if (progress_step > 13) {
          cl = "#ffff00";
        } else if (progress_step > 10) {
          cl = "#d5ff33";
        } else if (progress_step > 7) {
          cl = "#a2fa32";
        } else if (progress_step > 4) {
          cl = "#00f030";
        }

      if (x < maxsteps-progress_step+1) {
         bar = "<td bgcolor='" + cl + "' width=7px height=7px style='border:1px solid #D5D5D5'></td>" + bar;
      } else {
         bar = "<td bgcolor='#ffffff' width=7px height=7px style='border:1px solid #D5D5D5'></td>" + bar;
      }
    }

    healthbar += bar + "</table> <br><br>";
    var progress = "<p style='font-size:22px; font-family:Optima; color:#ff0000'>Steps: " +  parseInt(progress_step-1) + "</p>" + healthbar;

    var s = bigtable + "<td style=' border:none'>" + progress + " <table align='center' style='width:absolute; widthoat:center'>";

    calculate_seen();


    for (y = 0; y < height; y++) {
      s = s + "<tr>";
      for (x = 0; x < width; x++) {
 
        var w = parseInt(warr[y][x]);
        if ( x == ax && y== ay && wagent!=2) {
               s = s + "<td width=" + cellsize + "px height=" + cellsize +
                        "px><img src='webfile/agent.png' style='width:" + cellsize +
                        "px;height:" + cellsize + "px;display: block;'>";
        } else if ( x == ax && y== ay && wagent==2) {
               s = s + "<td width=" + cellsize + "px height=" + cellsize +
                        "px;>"
                       // "px;height:" + cellsize + "px;display: block;'>";
        }
        else if (w == 3) {
                s = s + "<td width=" + cellsize + "px height=" + cellsize + 
                        "px><img src='webfile/brickwall.png' style='width:" + cellsize + 
                        "px;height:" + cellsize + "px;display: block;'>";

        } else if (  ((seen[y][x] == 0 && w ==2) ||  w==5) && wagent !=2)  {
                s = s + "<td bgcolor='#3D3D3D' width=" + cellsize + "px height=" + cellsize + "px;";
        } else {

              bkc = "#ffffff";
              sclick = "tableClickedVoid";

              if ( wagent !=2 ) {
                  if (seen[y][x] == 1) {

                        if (x == ax-1 && y == ay) {
                                sclick = "tableClickedXminus";
                        } else if (x == ax+1 && y == ay) {
                                sclick = "tableClickedXplus";
                        } else if (y == ay-1 && x == ax) {
                                sclick = "tableClickedYminus";
                        } else if (y == ay+1 && x == ax) {
                                sclick = "tableClickedYplus";
                        }
 
                        bkc = "#ffffff";
                        if(w == 2) bkc = "#ff0000";
                  } else {
                         bkc = "#3D3D3D";
                  }
             }
             
             if (w==2 && seen[y][x] == 1 && wagent !=2) {
                  s = s + "<td bgcolor='#ff0000' width=" + cellsize + "px height=" + cellsize + "px onclick='"; 
                  s = s + sclick + "();'>";
               } else if (w==6) {
                  s = s + "<td bgcolor='#ffffff' width=" + cellsize + "px height=" + cellsize + "px onclick='";
                  s = s + sclick + "();'>";
               } else {
                  s = s + "<td bgcolor='";
                  s = s + bkc;
                  s = s + "' width=" + cellsize + "px height=" + cellsize + "px onclick='";
                  s = s + sclick;
                  s = s + "();'>";
              }
        } 
        s = s + "</td>";
      }
      s = s + "</tr>";
    }

    s = s + "</table>";

    s = s + "</td></table>";
    // alert("generate table"); // when the alerts do not show up it is something like a missing bracket that prevents compiling
    //if (wagent!=2 && progress_step < 29) s = s + "<p  align='center'> <button type=\"button\" disabled>Submit</button></p>";
    return s ;
}

function loadEventHandler() {
  //alert("load");
 
  if (valid == 1) {
    s = generate_table();
    document.getElementById("ex1_container").innerHTML = s;
    fitPasses = 0;
    // defer to allow DOM/layout to settle, then fit cells and rerender if needed
    setTimeout(computeCellSize, 0);
  }
  
  // Add keyboard event listeners
  document.addEventListener('keydown', handleKeyPress);

  // Recompute cell size on resize and re-render so the whole maze fits the screen
  window.addEventListener('resize', function() {
    computeCellSize();
  });
        
 /* var now= new Date(), 
  h= now.getHours(), 
  m= now.getMinutes(), 
  s= now.getSeconds();
  ms = now.getMilliseconds();

  var times = "t(" + h + "," + m + "," + s + "," + ms + ");";
  savedtime += times;*/
  oldtime = timestamp();  
}

function computeCellSize() {
    const marginSpace = 40; // buffer for UI, padding, progress bar
    const maxWidth = window.innerWidth * 0.98;
    const maxHeight = window.innerHeight * 0.80;

    // Calculate max cell size to fit both dimensions
    const cellSizeX = (maxWidth - marginSpace) / width;
    const cellSizeY = (maxHeight - marginSpace) / height;
    let newSize = Math.min(cellSizeX, cellSizeY);

    // Apply constraints: min 5px, max 38px
    newSize = Math.max(5, Math.floor(newSize));        // minimum usability
    newSize = Math.min(newSize-2, 38);                   // 🔴 maximum cell size = 38px

    if (!isFinite(newSize)) newSize = 5;

    // Only re-render if size changed
    const currentSize = parseInt(cellsize, 10);
    if (newSize !== currentSize) {
        cellsize = newSize;
        if (valid === 1) {
            document.getElementById("ex1_container").innerHTML = generate_table();
            // Optional: re-check after layout settles
            setTimeout(() => {
                computeCellSize(); // runs once more if needed (idempotent)
            }, 50);
        }
    }
}

// Compute cell size so the entire maze fits within viewport without scrolling
function computeCellSize_old() {
  var vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
  var vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);

  // Determine available space based on the actual maze container and progress block
  var mazeEl = document.querySelector('#ex1_container .maze-container');
  var progressEl = document.getElementById('progress_block');
  var rectTop = 0;
  var rectWidth = Math.floor(vw * 0.98);
  var progressHeight = 0;
  if (progressEl && typeof progressEl.getBoundingClientRect === 'function') {
    var prect = progressEl.getBoundingClientRect();
    progressHeight = Math.ceil(prect.height);
  }
  if (mazeEl && typeof mazeEl.getBoundingClientRect === 'function') {
    var mrect = mazeEl.getBoundingClientRect();
    rectTop = Math.max(0, mrect.top);
    rectWidth = Math.floor(mrect.width);
  }
  var maxW = rectWidth;
  var maxH = Math.floor(vh - rectTop - progressHeight - 1);
  if (maxH < 50) maxH = Math.floor(vh * 0.70);

  // Fit based on actual map dimensions, accounting for 1px grid lines
  var gridW = 1; // border width applied to each td with border-collapse: collapse
  var sizeX = Math.floor((maxW - gridW * (width + 1)) / width);
  var sizeY = Math.floor((maxH - gridW * (height + 1)) / height);
  var size = Math.min(sizeX, sizeY);
    // On MacBook Air and similar devices, reserve a few extra pixels to avoid any rounding-related clipping
    if ((/Macintosh|Mac OS X/).test(navigator.userAgent)) {
      size = Math.max(5, size - 1);
    }

  // Apply maximum and minimum cell size constraints
  var maxCellPx = 38; // cap to avoid oversizing
  if (size > maxCellPx) size = maxCellPx;
  if (!isFinite(size) || size <= 0) size = 5;

  var currentSize = (typeof cellsize === 'string') ? (parseInt(cellsize, 10) || 5) : cellsize;
  var newSize = Math.max(5, size);
  if (newSize !== currentSize) {
    cellsize = newSize;
    if (valid == 1) {
      document.getElementById("ex1_container").innerHTML = generate_table();
      fitPasses += 1;
      if (fitPasses < 2) {
        setTimeout(computeCellSize, 0);
      }
    }
  }
}

// Handle keyboard input
function handleKeyPress(event) {
  // Prevent default behavior for arrow keys and WASD
  if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'KeyW', 'KeyA', 'KeyS', 'KeyD'].includes(event.code)) {
    event.preventDefault();
  }
  
  // Only process if we haven't reached max steps
  if (progress_step >= maxsteps) {
    return;
  }
  
  switch(event.code) {
    case 'ArrowUp':
    case 'KeyW':
      if (ay > 0 && parseInt(warr[ay-1][ax]) !== 3) {
        ay = ay - 1;
        tableClicked();
      }
      break;
    case 'ArrowDown':
    case 'KeyS':
      if (ay < height - 1 && parseInt(warr[ay+1][ax]) !== 3) {
        ay = ay + 1;
        tableClicked();
      }
      break;
    case 'ArrowLeft':
    case 'KeyA':
      if (ax > 0 && parseInt(warr[ay][ax-1]) !== 3) {
        ax = ax - 1;
        tableClicked();
      }
      break;
    case 'ArrowRight':
    case 'KeyD':
      if (ax < width - 1 && parseInt(warr[ay][ax+1]) !== 3) {
        ax = ax + 1;
        tableClicked();
      }
      break;
  }
}

function tableClickedVoid() {

}

function tableClickedXminus() {
 if (progress_step < maxsteps) {
   ax = ax-1;
   tableClicked();
 }
}

function tableClickedXplus() {
 if (progress_step < maxsteps) {
   ax = ax+1;
   tableClicked();
 }
}

function tableClickedYminus() {
 if (progress_step < maxsteps) {
   ay = ay-1;
   tableClicked();
 }
}

function tableClickedYplus() {
 if (progress_step < maxsteps) {
   ay = ay+1;
   tableClicked();
 }
}


function tableClicked() {
        progress_step = progress_step + 1;
	jssteps = jssteps+1;
        savedpath += "p(" + ax + "," + ay + ");";

        var newtime = timestamp();
        var difference = newtime - oldtime;
        oldtime = newtime;
        savedtime = savedtime + difference + ";"; 

        /*var now= new Date(), 
        h= now.getHours(), 
        m= now.getMinutes(), 
        s= now.getSeconds();
        ms = now.getMilliseconds();

        times = "t(" + h + "," + m + "," + s + "," + ms + ");";
        savedtime += times;  
*/
	var w = parseInt(warr[ay][ax]);

        document.getElementById("ex1_container").innerHTML = generate_table(w);
        fitPasses = 0;
        setTimeout(computeCellSize, 0);
        // if reached goal state.
	
       snext = "thanks_solve.php";

        if (w == 2 ) {
                
            if (mn < num_test + num_practice - 1 ) {
                            snext = 'test.php';
            }

            if (mn == num_practice-1) {
                snext = 'planning_quiz.php';
            }

            document.getElementById("ex1_container").innerHTML += "<p align='center'><form name='frm' action='" + snext + 
                                  "' method='post' onsubmit='submitForm()'>" + 
                                  "<input type='text' name='name' hidden><input type='text' name='steps' hidden>" + 
                                  "<input type=text' name='mazeno' hidden><input type='text' name='UID' hidden>" +      
                                  "<input type=text' name='showEndNext' hidden><input type=text' name='firsttrial' hidden>" +
                                  "<input type='text' name='path' hidden><input type='text' name='time' hidden><input type='text' name='mazeID' hidden>" +
                                  "</form></p>";

                        
                        submitForm();
                        document.forms["frm"].submit();  
	} 
        
}


function submitForm() {
        document.forms["frm"]["steps"].value = jssteps;       
        document.forms["frm"]["UID"].value = u_id;
        document.forms["frm"]["mazeno"].value = mn;
        document.forms["frm"]["mazeID"].value = mnr;
        document.forms["frm"]["path"].value = savedpath;
        document.forms["frm"]["time"].value = savedtime;
        document.forms["frm"]["name"].value = mf; 
        document.forms["frm"]["showEndNext"].value = showEnd;
        //document.forms["frm"]["firsttrial"].value = "false";
}
</script>

<div id="ex1_container">
</div>

</body>
</html> 
