<?php
	require 'php/EEB_SQLITE3.php';
	require 'php/EEB_UI.php';

	session_start();

	$ui = new EEB_UI;   // default user interface

	// define the sql file path
	if ($_POST['num_package'] != NULL) {      
		$cur_model = $_SESSION['cur_model'] = $_POST['num_package'];
	} elseif($_POST['num_package'] == NULL && $_SESSION['cur_model'] == NULL) {
		$cur_model = $_SESSION[cur_model];
	} else {
		$cur_model = $_SESSION[cur_model];
	}
  
  // baseline sql file path
  if($cur_model == $_SESSION['Model'][0]) {
	   $sql_file="ENERGYPLUS/idf/{$cur_model}/EnergyPlusPreProcess/EnergyPlus-0/eplusout.sql";
  } else { // eem sql file path
     $sql_file="eem/$_SESSION[user_dir]/Output/{$cur_model}.sql";
  }

	$eeb = new EEB_SQLITE3("$sql_file");
	$e_vals = $eeb->getValuesByMonthly('END USE ENERGY CONSUMPTION ELECTRICITY MONTHLY', 'Meter', '', '%');
	$ng_vals = $eeb->getValuesByMonthly('END USE ENERGY CONSUMPTION NATURAL GAS MONTHLY', 'Meter', '', '%');

  // echo $eeb->getFilePath();
	function printRow($row){
		foreach($row as $v) {
			if($v >=0) {
				echo "<td> $v </td>";
			} else {
				echo "<td> 0.0 </td>";
			}
		}
	}

	function printMonthlyData($row){
		echo '[';
		foreach($row as $v) {
			if($v > 0)
				echo "$v, ";
			else
				echo "0.0, ";
		}
		echo ']';
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>EEB Hub Simulation Tools: Comprehensive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/docs.css" rel="stylesheet">
	<link href="css/comprehensive.css" rel="stylesheet">    

    <style>
    </style>
  </head>

  <body>

    <!-- Navbar 
    ================================================== -->
    <? $ui->drawNavbar();?>

    <!-- Container -->
    <div class="container">

    <!-- Switch Pacakge -->
    <? $ui->drawSwitchPackage();?>

        <!-- Sub-Nav-bar -->
        <? $page[energy]="active"; $ui->drawSubNavbar($page); ?>
 

      <!-- Tab Content -->
        <!-- Electricity Consumption Bar Chart -->
        <div id="Electricity-Consumption-Bar-Chart" style="width: 50%; min-width: 400px; height: 500px; float:left;"></div>
         
        <!-- Natural Gas Consumption Bar Chart -->
        <div id="Natural-Gas-Consumption-Bar-Chart" style="width: 50%; min-width: 400px; height: 500px; float: left; margin-bottom: 25px;"></div>

        <!-- Electricity Consumption Table -->
        <table class="table table-striped table-bordered" style="margin: 40px auto; width: 100%">
          <caption style="background: purple; color: #fff;"> <h3>Electricity Energy Consumption (kWh)<h3> </caption>
          <tr id="table-row-head">
            <th> -
            </th>
            <th> Jan
            </th>
            <th> Feb
            </th>
            <th> Mar
            </th>
            <th> Apr
            </th>
            <th> May
            </th>
            <th> Jun
            </th>
            <th> Jul
            </th>
            <th> Aug
            </th>
            <th> Sep
            </th>
            <th> Oct
            </th>
            <th> Nov
            </th>
            <th> Dec
            </th>
          </tr>
          <tr class="table-row-even">
            <th> Heating
            </th>
           	<?php printRow($e_vals['HEATING:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-odd">
            <th> Cooling
            </th>
            <?php printRow($e_vals['COOLING:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-even">
            <th> Interior Lighting
            </th>
           	<?php printRow($e_vals['INTERIORLIGHTS:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-odd">
            <th> Interior Equipment
            </th>
            <?php printRow($e_vals['INTERIOREQUIPMENT:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-even">
            <th> Fans
            </th>
             <?php printRow($e_vals['FANS:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-odd">
            <th> Pumps
            </th>
            <?php printRow($e_vals['PUMPS:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-even">
            <th> Heat Rejection
            </th>
            <?php printRow($e_vals['HEATREJECTION:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-odd">
            <th> Humidification
            </th>
            <?php printRow($e_vals['HUMIDIFIER:ELECTRICITY']); ?>
          </tr>
          <tr class="table-row-even">
            <th> Heat Recovery
            </th>
             <?php printRow($e_vals['HEATRECOVERY:ELECTRICITY']); ?>
          </tr>
			<tr class="table-row-odd">
		    <th> Water Systems
		    </th>
		    <?php printRow($e_vals['HUMIDIFIER:ELECTRICITY']); ?>

		  </tr>
		  <tr class="table-row-even">
		    <th> Regrigeration
		    </th>
		     <?php printRow($e_vals['HEATRECOVERY:ELECTRICITY']); ?>
		  </tr>
 		  <tr class="table-row-even">
		    <th> Generation
		    </th>
		     <?php printRow($e_vals['HEATRECOVERY:ELECTRICITY']); ?>
		  </tr>
          </table>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->

    <!-- load highchart libs -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/Highcharts-3.0.4/js/highcharts.js"></script>
    <script src="js/Highcharts-3.0.4/js/modules/exporting.js"></script>
     <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <!-- Charts' Defination -->
    <script>
      // Eletricity Consumption Bar Chart Defination-->
      $(function () {
              $('#Electricity-Consumption-Bar-Chart').highcharts({
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Electricity Consumption (kWh)'
                  },
                  xAxis: {
                      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: '(x0000) '
                      },
                      stackLabels: {
                          enabled: true,
						  rotation: -45,
						  y: -20,
                          style: {
                              fontWeight: 'bold',
                              color: (Highcharts.theme && Highcharts.theme.textColor) || 'grey'
                          }
                      }
                  },
                  legend: {
                      align: 'center',
                      x: 0,
                      verticalAlign: 'bottom',
                      y: 0,
                      floating: false,
                      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
                      borderColor: '#CCC',
                      borderWidth: 1,
                      shadow: true
                  },
                  tooltip: {
                      formatter: function() {
                          return '<b>'+ this.x +'</b><br/>'+
                              this.series.name +': '+ this.y +'<br/>'+
                              'Total: '+ this.point.stackTotal;
                      }
                  },
                  plotOptions: {
                      column: {
                          stacking: 'normal',
                          dataLabels: {
                              enabled: false,
                              color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                          }
                      }
                  },
                  series: [{
                      name: 'Pumps',
                      data: <?php printMonthlyData($e_vals['PUMPS:ELECTRICITY']);?>
                  },{
                      name: 'Fans',
                      data: <?php printMonthlyData($e_vals['FANS:ELECTRICITY']);?>
                  }, {
                      name: 'Cooling',
                      data: <?php printMonthlyData($e_vals['COOLING:ELECTRICITY']);?>
                  }, {
                      name: 'Interior Lighting',
                      data: <?php printMonthlyData($e_vals['INTERIORLIGHTS:ELECTRICITY']);?>
                  }, {
                      name: 'Interior Equipment',
                      data: <?php printMonthlyData($e_vals['INTERIOREQUIPMENT:ELECTRICITY']);?>
                  }, {
                      name: 'Heating',
                      color: 'red',
                       visible: false,
                      data: <?php printMonthlyData($e_vals['HEATING:ELECTRICITY']);?>
                  }, {
                      name: 'Exterior Lighting',
                 	  visible: false,
                      data: <?php printMonthlyData($e_vals['EXTERIORLIGHTS:ELECTRICITY']);?>
                  }, {
                      name: 'Heat Rejection',
                      color: 'orange',
	 				  visible: false,
                      data: <?php printMonthlyData($e_vals['HEATREJECTION:ELECTRICITY']);?>
                  }, {
                      name: 'Humidification',
                   visible: false,
                      data: <?php printMonthlyData($e_vals['HUMIDIFICATION:ELECTRICITY']);?>
                  }, {
                      name: 'Heating Recovery',
                      visible: false,
                      data: <?php printMonthlyData($e_vals['HEATINGRECOVERY:ELECTRICITY']);?>
                  },{
                      name: 'Water Systems',
                       visible: false,
                      data: <?php printMonthlyData($e_vals['WATERSYSTEMS:ELECTRICITY']);?>
                  }, {
                      name: 'Refrigeration',
                      visible: false,
                      data: <?php printMonthlyData($e_vals['REGRIGERATION:ELECTRICITY']);?>
                  }, {
                      name: 'Generation',
                      visible: false,
                      data: <?php printMonthlyData($e_vals['GENERATION:ELECTRICITY']);?>
                  }]
              });
      });

      // Natural Gas Consumption Bar Chart Defination-->
      $(function () {
              $('#Natural-Gas-Consumption-Bar-Chart').highcharts({
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Natural Gas Consumption (MBtu)'
                  },
                  xAxis: {
                      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: '(x000,000)'
                      },
                      stackLabels: {
                          enabled: true,
                          style: {
                              fontWeight: 'bold',
                              color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                          }
                      }
                  },
                  legend: {
                      align: 'center',
                      x: 0,
                      verticalAlign: 'bottom',
                      y: 0,
                      floating: false,
                      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
                      borderColor: '#CCC',
                      borderWidth: 1,
                      shadow: true
                  },
                  tooltip: {
                      formatter: function() {
                          return '<b>'+ this.x +'</b><br/>'+
                              this.series.name +': '+ this.y +'<br/>'+
                              'Total: '+ this.point.stackTotal;
                      }
                  },
                  plotOptions: {
                      column: {
                          stacking: 'normal',
                          dataLabels: {
                              enabled: false,
                              color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                          }
                      }
                  },
                  series: [{
                      name: 'Heating',
                      color: 'red',
                      data: <?php printMonthlyData($ng_vals['HEATING:GAS']);?>
                  }]
              });
      });
    </script>
  </body>
</html>
