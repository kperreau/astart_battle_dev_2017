<?php
//ini_set('memory_limit', '-1');
//$input = file('/var/www/html/opteamiser/test/input6.txt');

$size = array_shift($input) - 1;

$map = array();
$c = array();
$m = array();
$o = array();

$y = 0;
foreach($input as $v)
{
	$l = str_split(str_replace("\n", "", $v));
	$map[] = $l;
	$x = 0;
	foreach($l as $i)
	{
		if ($i == 'O')
			$o = array($x, $y);
		elseif ($i == 'C')
			$c = array($x, $y);
		elseif ($i == 'M')
			$m[] = array($x, $y);
		++$x;
	}
	++$y;
}

class PQ extends SplPriorityQueue
{
    public function compare($priority1, $priority2)
    {
        return $priority1 > $priority2 ? -1 : 1;
    }
}

class Node
{
	Public	$x;
	Public	$y;
	Public	$score;
	Public	$dist;
	Public	$count;
	Public	$node;
	Public	$parent;
	
	public	function __construct($x, $y, $count, $end, &$parent = null)
	{
		$this->x = $x;
		$this->y = $y;
		$this->parent = $parent;
		$this->count = $count + 1;
		if ($end != null)
			$this->heuristic($end);
		else
			$dist = 0;
		$this->score = $this->count + $this->dist;
	}
	
	public	function	heuristic(Node &$end)
	{
		global $map;
		global $size;
		$x1 = abs($end->x - $this->x);
		$x2 = min(array($end->x, $this->x)) + ($size - max(array($end->x, $this->x))) + 1;
		$y1 = abs($end->y - $this->y);
		$y2 = min(array($end->y, $this->y)) + ($size - max(array($end->y, $this->y))) + 1;

		$this->dist = (($x1 < $x2) ? $x1 : $x2);
		$this->dist += (($y1 < $y2) ? $y1 : $y2);
	}
}

function	moove($x, $y, &$current, &$open, &$close, &$end, &$node)
{
	global $map;
	global $size;

	$cx = $current->x + $x;
	$cy = $current->y + $y;
	
	if (($current->x + $x) < 0 || ($current->x + $x) > $size)
		$cx = ($current->x + $x) < 0 ? $size : 0;
	if (($current->y + $y) < 0 || ($current->y + $y) > $size)
		$cy = ($current->y + $y) < 0 ? $size : 0;
	
	if (($map[$cy][$cx] == "." || $map[$cy][$cx] == "O")
		&& !isset($close[$cx][$cy]))
	{
		$tmp = new Node($cx, $cy, $current->count, $end, $current);
		$node[] = $tmp;
	}
}

$lol = null;
function	astar(Node &$start, Node &$end)
{
	global $map;
	global $size;
	global $lol;
	$close = null;
	$open = new PQ();
	
	$close[$start->x][$start->y] = $start;
	$current = $start;
	while (1)
	{
		$node = null;
		if ($current->dist == 0)
			break ;
		moove(-1, 0, $current, $open, $close, $end, $node);
		moove(1, 0, $current, $open, $close, $end, $node);
		moove(0, -1, $current, $open, $close, $end, $node);
		moove(0, 1, $current, $open, $close, $end, $node);
		if (!empty($node))
		{
			foreach($node as $val)
				$open->insert($val, $val->score);
		}
		if ($open->isEmpty())
			return (-1);
		
		$current = $open->extract();
		$close[$current->x][$current->y] = $current;
	}
	$lol = ($current->score);
	// echo "open: ", $open->count(), "\n";
	// echo "close: ", count($close), "\n\n";
	return (1);
}

function	main()
{
	global	$map;
	global	$o;
	global	$c;
	global	$m;
	global	$lol;
	$f = 0;
	$g = 9999;

	$end = new Node($o[0], $o[1], 0, null);
	$start = new Node($c[0], $c[1], -1, $end);
	// echo $start->score,"\n";
	if (astar($start, $end) == -1)
	{
		// echo "Impossible.\n";
	}
	$g = $lol;
	foreach ($m as $l)
	{
		$start = new Node($l[0], $l[1], -1, $end);

		if (astar($start, $end) == -1)
		{
			// echo "Impossible.\n";
		}
		elseif ($lol <= $g)
			$f = 1;
	}
	echo !$f ? $g : 0;

}
main();
?>
