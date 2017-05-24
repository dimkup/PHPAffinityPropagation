## About

This is a PHP class, that implements affinity propagation clustering algorithm. It's based on this implementation: https://github.com/jincheng9/AffinityPropagation There are only two dimensions and  the Eculidean distance. See an excelent presentation on the affinity propagation algorithm [here](http://www.igi.tugraz.at/lehre/MLA/WS07/MLA_AffinityPropagation.pdf).

## Usage

Constructor takes two parameters: 
- number of iterations
 - dumping factor

The method `split` takes an array of points in the following format:

```
array(array("id"=>1,"x"=>1,"y"=>1), array(...), ... )
```

where:
- id=> uinq id of this point, can be anything
- x,y=> coordinates of the point, must be numbers

The method returns array of clusters. The Ids of the clusters' centroids are the keys. Arrays of Ids of the cluster members are the values. 

## Example
### Code:
```
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
```
### Output:
```
array(2) {
  [1]=>
  array(3) {
    [0]=>
    int(1)
    [1]=>
    int(3)
    [2]=>
    int(4)
  }
  [6]=>
  array(4) {
    [0]=>
    int(2)
    [1]=>
    int(5)
    [2]=>
    int(6)
    [3]=>
    int(7)
  }
}
```