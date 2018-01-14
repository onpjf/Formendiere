    <?php
    require_once ("jpgraph-3.5.0b1/src/jpgraph.php");
    require_once ("jpgraph-3.5.0b1/src/jpgraph_date.php");
	require_once ("jpgraph-3.5.0b1/src/jpgraph_line.php");
    function graph($title,$time,$datay1,$color1,$texty1,$datay2=null,$color2=null,$texty2=null)
    {
      //$graph = new Graph(275,150);
	  $graph = new Graph(800,600);
      $graph->SetMargin(40,40,10,100);
      $graph->SetMarginColor('white');
     
	  $graph->SetScale('datint');
	  
	//$graph->xaxis->scale->SetTimeAlign(HOURADJ_2);
 
	
	// Use hour:minute format for the labels
	  //$graph->xaxis->scale->SetDateFormat('D d H');
	  $graph->xaxis->SetLabelFormatString('D d, H',true);
      $graph->img->SetAntiAliasing();
      $graph->title->Set($title);
      $graph->SetBox(false);
      $graph->yaxis->HideZeroLabel();
      $graph->yaxis->HideTicks(false,false);
      $graph->yaxis->title->Set($texty1);
	  //$graph->yaxis->scale->SetAutoMax(25);
      // on prend le mini des données -2 pour l'axe des Y, cela permet d'avoir la température toujours au dessus des X
	  $graph->yaxis->scale->SetAutoMin(min($datay1)-2);
	  $graph->yaxis->scale->SetAutoMax(max($datay1)+1);
	$graph->xaxis->SetTickLabels($time);
      
	  $graph->xaxis->SetLabelAngle(90);
      //$graph->xaxis->title->Set('Date');
      $p1=new LinePlot($datay1,$time);
      $graph->Add($p1);
      $p1->SetColor($color1);
      $graph->yaxis->SetColor('darkgray');
      if (isset($datay2)&&isset($color2)&&isset($texty2))
      {   
        $graph->SetYScale(0,'lin');
        $graph->ynaxis[0]->title->Set($texty2);
       
        $graph->ynaxis[0]->SetTickSide(SIDE_RIGHT);   
        $p2=new LinePlot($datay2,$time);
        $graph->AddY(0,$p2);
       
        $p2->SetColor($color2);
        $graph->ynaxis[0]->SetColor('darkgray');
        $graph->ynaxis[0]->title->SetColor($color2);
      }
      $graph->yaxis->title->SetColor($color1);
      @unlink("g$title.png"); //suppression du graphe precedent
      $graph->Stroke("g$title.png");
    }
	
	
    ?>