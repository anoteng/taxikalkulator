<!DOCTYPE html>
<?php
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Distrikt */
define("D_START", '48');
define("D_START_FREMMØTE", '82');
define("D_KM", '10.2');
define("D_MIN", '7.15');
define("D_TILKJORING", '17.40');
define("D_KM_AVSTAND", '18.10');
define("D_MINSTEPRIS", '109');

/* Trondheim */
define("T_START", '45');
define("T_KM", '11.73');
define("T_MIN", '9.27');
define("T_MINSTEPRIS", '127');
define("T_MILJOPAKKEN", '8');
define("T_MILJOPAKKEN_9", '24');

define("TAKSTER_OPPDATERT", '08.04.2019');
define("VERSJON", '0.1a');

if(isset($_POST['kilometer']) AND $_POST['sone'] == "distrikt"){


	if($_POST['kilometer'] > 10){
		$langtakst = 1;
		$km = 10;
	} else {
		$km = $_POST['kilometer'];
		$langtakst = 0;
	}
	if($_POST['henting']){
		$henting = 1;
		$starttakst = 48;
	} else {
		$starttakst = 82;
		$henting = 0;
	}
	if($_POST['fremkjoring'] > 10){
		$fremkjoring = $_POST['fremkjoring'];
	} else {
		$fremkjoring = 0;
	}
	$has_kolon = stripos($_POST['kjoretid'], ':') !== false;
	if($has_kolon){
		$time = explode(':', $_POST['kjoretid']);
		$minutter = $time[0]*60 + $time[1];
	} else {
		$minutter = $_POST['kjoretid'];
	}
	$pris = $starttakst +  ($km * D_KM) + ($langtakst * (($_POST['kilometer'] - 10) * D_KM_AVSTAND)) + ($henting * ($fremkjoring * D_TILKJORING)) + (D_MIN * $minutter);
	/* echo $starttakst ." + ".  $km ." * ". D_KM ." + ". $langtakst ." * ((". $_POST['kilometer'] ."- 10) * ". D_KM_AVSTAND .") + ". $henting ." * (". $fremkjoring ." * ". D_TILKJORING .") + ". D_MIN ." * ". $minutter ."<br />"; */
	if($pris < D_MINSTEPRIS) {
		$pris = D_MINSTEPRIS;
	}
	if($_POST['takst'] == 5){
		$pris = $pris * 1.5;
	} elseif($_POST['takst'] == 9){
		$pris = $pris * 2;
	}
	
	$tid = explode(':', $_POST['tid']);
	if($_POST['ukedag'] == 'U'){
		if($tid[0] >= 18){
			$pris = $pris * 1.21;
		} elseif($tid[0] >= 0 and $tid[0] < 6) {
			$pris = $pris * 1.35;
		}
	} elseif($_POST['ukedag'] == 'L'){
		if($tid[0] >= 6 AND $tid[0] < 15){
			$pris = $pris * 1.3;
		}else{
			$pris = $pris * 1.35;
		}
	} elseif($_POST['ukedag'] == 'S'){
		$pris = $pris * 1.35;
	} elseif($_POST['ukedag'] == 'H'){
		$pris = $pris * 1.45;
	}
	/*echo round($pris);*/
} elseif(isset($_POST['kilometer']) AND $_POST['sone'] == "trondheim") {
	$has_kolon = stripos($_POST['kjoretid'], ':') !== false;
	if($has_kolon){
		$time = explode(':', $_POST['kjoretid']);
		$minutter = $time[0]*60 + $time[1];
	} else {
		$minutter = $_POST['kjoretid'];
	}
	$pris = T_START + ($_POST['kilometer'] * T_KM) + (T_MIN * $minutter);
	/*echo T_START ." + (". $_POST['kilometer'] ." * ". T_KM .") + (". T_MIN ." * ". $minutter .")";*/
	if($pris < T_MINSTEPRIS) {
		$pris = T_MINSTEPRIS;
	}
	if($_POST['takst'] == 5){
		$pris = $pris * 1.5;
		$pris = $pris + T_MILJOPAKKEN;
	} elseif($_POST['takst'] == 9){
		$pris = $pris * 2;
		$pris = $pris + T_MILJOPAKKEN_9;
	} else {
		$pris = $pris + T_MILJOPAKKEN;
	}
	/*echo round($pris);
	echo $_POST['tid']; */
}
	
?>

<html lang="nb">
  <head>
    <title>Uoffisiell priskalkulator, Trøndertaxi</title>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
    <link href="style.css" rel="stylesheet">
    <!-- <script src="script.js"></script> -->
    <script>
    function valueChanged()
    {
        if($('.henting').is(":checked"))   
            $(".fremkjoring").show();
        else
            $(".fremkjoring").hide();
    }

	$(function() {
	  $('#sone').change(function(){
		if($(this).val() == 'trondheim')
			$('.distrikt').hide();
		else
			$('.distrikt').show();
	  });
	});
   </script>
  </head>
  <body>
    <div id="mainform">
      <div class="innerdiv">
      <h2>Uoffisiell priskalkulator Trøndertaxi</h2>
      <form id="form" name="form" action="" method="POST">
      <div>
		<p>
			<label for="sone">takstområde:</label>
			<select name="sone" id="sone">
			  <option value="distrikt">Distrikt</option>
			  <option value="trondheim">Trondheim</option>
			</select>
		</p>
        <p>
          <label>Kilometer:</label><br />
          <input id="kilometer" type="text" name="kilometer">
        </p>

          <label>Kjøretid:</label><br />
	  <input id="kjoretid" type="text" name="kjoretid">
        </p>
        <p>
          <label>Tid :</label><br />
          <input id="tid" type="time" value="<?php echo date("H:i:s") ?>" name="tid">
        </p>
		<p>
		<label for="ukedag">Ukedag: </label><br />
		<select name="ukedag" id="ukedag">
			<option value="U">Mandag-fredag</option>
			<option value="L">Lørdag</option>
			<option value="S">Søndag</option>
			<option value="H">Hellidagstakst</option>
		</select>
		</p>
		<p>
		<label for="takst">Takst: </label><br />
		<select name="takst" id="takst">
			<option value="1">1:4</option>
			<option value="5">5:8</option>
			<option value="9">9:16</option>
		</select>
		</p>
		<p>
          <label class="distrikt">Henting?</label><input class="henting distrikt" id="henting" type="checkbox" checked onchange="valueChanged()" name="henting">
          <label class="fremkjoring distrikt">Kilometer fremkjøring:</label><br />
          <input class="fremkjoring distrikt" type="text" id="fremkjoring" name="fremkjoring"></p>
        <p>
		<input id="submit" type="submit" name="submit" value="Submit">
		<?php
		echo $pris ."<br />";
		echo round($pris);
		?>
      </div>
    </form>

    </div>
    </div>
	<div class="disclaimer">
		Priskalkulatoren er laget for at sjåfører raskere skal kunne regne ut forhåndspris/makspris for tur uten å måtte logge inn på ekstranett, også i tilfeller hvor app kommer til kort (flere enn 4 passasjerer, adresse ikke riktig registrert eller finnes ikke i gogle maps). Merk at kalkulatoren gir eksakt pris for en tur med de oppgitte variablene. Du må selv ta høyde for trafikkale forhold, eller annet som kan føre til lengere kjørelengde eller høyere tidsbruk.<br />
		Kalkulatoren benyttes på eget ansvar, og det gis ingen garantier for riktig pris.<br />
		Takstinformasjon sist oppdatert <?php echo TAKSTER_OPPDATERT; ?>.<br />
		Taxikalkulatoren versjon <?php echo VERSJON; ?>.<br />
	</div>
	<footer>
	<p>
		Copyright &copy; 2019 Andreas Noteng<br />
		This program comes with ABSOLUTELY NO WARRANTY;<br />
		for details see the <a href="https://www.gnu.org/licenses/gpl.html">GNU General Public License</a>, sections 15 through 17.<br />
		This is free software, and you are welcome to redistribute it<br />
		under certain conditions listed in the GNU General Public License.<br />
		The source code for this project can be found on <a href="https://github.com/anoteng/taxikalkulator">GitHub</a>.
	</p>
	</footer>

  </body>
</html>
