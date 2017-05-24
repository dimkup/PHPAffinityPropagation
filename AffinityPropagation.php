#!/usr/local/bin/php

<?php

//Realization of Affinity Propagation clustering algorithm 
class AffinityPropagation {
	
	protected $iter;
	protected $dumping;
	public $debug = FALSE;
	
	protected $S;
	protected $R;
	protected $A;
	protected $N;
	
	//Calss constructor. $iter is number of iterations, $dumping is offset to prevent numerical oscillations
	function __construct($iter,$dumping) {
		   $this->iter = $iter;
		   $this->dumping = $dumping;
	}
	
	function init_arrays($terms) {
		
		$this->N = count($terms);
		$this->A = array_fill(0, $this->N, array_fill(0, $this->N, 0));
		$this->R = array_fill(0, $this->N, array_fill(0, $this->N, 0));
		$this->S = array_fill(0, $this->N, array_fill(0, $this->N, 0));
		
		$N = $this->N; //alias
		
		$size = $N*($N-1)/2;
		$tmpS = array();
		
		//Compute similarity as -((x1-x2)^2+(y1+y2)^2)	
		for($i=0; $i<$N-1; $i++) {
			for($j=$i+1; $j<$N; $j++) {
				$this->S[$i][$j] = -
					(($terms[$i]["x"]-$terms[$j]["x"])*($terms[$i]["x"]-$terms[$j]["x"])
						+($terms[$i]["y"]-$terms[$j]["y"])*($terms[$i]["y"]-$terms[$j]["y"]));
				$this->S[$j][$i] = $this->S[$i][$j];
				$tmpS[] = $this->S[$i][$j]; 
			}
		}
		
		//compute median for self similarity
		sort($tmpS);
		$median = 0;
		if($size % 2==0) 
				$median = ($tmpS[$size/2]+$tmpS[$size/2-1])/2;
			else 
				$median = $tmpS[$size/2];
		for($i=0; $i<$N; $i++) $this->S[$i][$i] = $median;
		
	}
	
	   
	//function that do the job
	function split($terms){
		
		//init arrays
		$this->init_arrays($terms);
		
		$N = $this->N; //alias
		
		//Strat iterations
		for($m=0; $m<$this->iter; $m++) {
			//update responsibility
				for($i=0; $i<$N; $i++) {
					for($k=0; $k<$N; $k++) {
						$max = -pow(10,100);
						for($kk=0; $kk<$k; $kk++) {
							if($this->S[$i][$kk]+$this->A[$i][$kk]>$max) 
								$max = $this->S[$i][$kk]+$this->A[$i][$kk];
						}
						for($kk=$k+1; $kk<$N; $kk++) {
							if($this->S[$i][$kk]+$this->A[$i][$kk]>$max) 
								$max = $this->S[$i][$kk]+$this->A[$i][$kk];
						}
						$this->R[$i][$k] = (1-$this->dumping)*($this->S[$i][$k] - $max) + $this->dumping*$this->R[$i][$k];
					}
				}
				
			//update availability
				for($i=0; $i<$N; $i++) {
					for($k=0; $k<$N; $k++) {
						if($i==$k) {
							$sum = 0.0;
							for($ii=0; $ii<$i; $ii++) {
								$sum += max(0.0, $this->R[$ii][$k]);
							}
							for($ii=$i+1; $ii<$N; $ii++) {
								$sum += max(0.0, $this->R[$ii][$k]);
							}
							$this->A[$i][$k] = (1-$this->dumping)*$sum + $this->dumping*$this->A[$i][$k];
						} else {
							$sum = 0.0;
							$maxik = max($i, $k);
							$minik = min($i, $k);
							for($ii=0; $ii<$minik; $ii++) {
								$sum += max(0.0, $this->R[$ii][$k]);
							}
							for($ii=$minik+1; $ii<$maxik; $ii++) {
								$sum += max(0.0, $this->R[$ii][$k]);
							}
							for($ii=$maxik+1; $ii<$N; $ii++) {
								$sum += max(0.0, $this->R[$ii][$k]);
							}
							$this->A[$i][$k] = (1-$this->dumping)*min(0.0, $this->R[$k][$k]+$sum) + $this->dumping*$this->A[$i][$k];
						}
					}
				}
			}
	
			//find the exemplar
			$center = array();
			for($i=0; $i<$N; $i++) {
				if($this->R[$i][$i] + $this->A[$i][$i]>0) {
					$center[]=$i;
				}
			}
			

			//data point assignment, idx[i] is the exemplar for the data point i
			$idx = array_fill(0, $this->N, 0);
			for($i=0; $i<$N; $i++) {
				$idxForI = 0;
				$maxSim = -pow(10,100);
				for($j=0; $j<count($center); $j++) {
					$c = $center[$j];
					if ($this->S[$i][$c]>$maxSim) {
						$maxSim = $this->S[$i][$c];
						$idxForI = $c;
					}
				}
				$idx[$i] = $idxForI;
			}
			//output the assignment
			$OUT = array();
			foreach ($center as $k) {
				$OUT[$terms[$k]["id"]]=array();
			}
			for($i=0; $i<$N; $i++) {
				$OUT[$terms[$idx[$i]]["id"]][]=$terms[$i]["id"];
			}
		
		
		return $OUT;
	}
}

$m = new AffinityPropagation(230,0.5);

$o = $m->split(array(
	array("id"=>1,"x"=>1,"y"=>1),
	array("id"=>2,"x"=>103,"y"=>104),
	array("id"=>3,"x"=>2,"y"=>1),
	array("id"=>4,"x"=>1,"y"=>2),
	array("id"=>5,"x"=>100,"y"=>100),
	array("id"=>6,"x"=>102,"y"=>102),
	array("id"=>7,"x"=>101,"y"=>101)));
	
var_dump($o);

echo "\n";

?>