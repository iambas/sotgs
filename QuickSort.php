<?php

class QuickSort
{
	private $data;

	function __construct($data)
	{
		$this->data = $data;
	}

	public function getDataSort()
	{
		return $this->data;
	}

	public function sort()
	{
		$this->qSort(0, count($this->data)-1);
	}

	private function qSort($left, $right)
	{
		if ($left >= $right)	return;
		$pivot = $left;
		for($i = $left; $i <= $right; $i++)
		{
			if ($this->data[$i][2] > $this->data[$left][2])
				$this->swapNormal(++$pivot, $i);
		}

		$this->swapNormal($pivot, $left);
		$this->qSort($left, $pivot-1);
		$this->qSort($pivot+1, $right);
	}

	private function swapNormal($x, $y)
	{
		for($i = 0; $i < 3; $i++){
			$temp = $this->data[$x][$i];
			$this->data[$x][$i] = $this->data[$y][$i];
			$this->data[$y][$i] = $temp;
		}
	}

	public function sortTable($col, $cnSort) {
		if ($cnSort % 2 == 0){
			$this->qSortAsc(0, count($this->data) - 1, $col);
		}else{
			$this->qSortDesc(0, count($this->data) - 1, $col);
		}
	}

	private function qSortAsc($left, $right, $col) {
		if ($left >= $right) return;
		$pivot = $left;
		for ($i = $left; $i <= $right; $i++) {
			if ($this->data[$i][$col] < $this->data[$left][$col])
				$this->swap(++$pivot, $i);
		}
		$this->swap($pivot, $left);
		$this->qSortAsc($left, $pivot - 1, $col);
		$this->qSortAsc($pivot + 1, $right, $col);
	}

	private function qSortDesc($left, $right, $col) {
		if ($left >= $right) return;
		$pivot = $left;
		for ($i = $left; $i <= $right; $i++) {
			if ($this->data[$i][$col] > $this->data[$left][$col])
				$this->swap(++$pivot, $i);
		}
		$this->swap($pivot, $left);
		$this->qSortDesc($left, $pivot - 1, $col);
		$this->qSortDesc($pivot + 1, $right, $col);
	}

	private function swap($x, $y) {
		for($i = 0; $i < 4; $i++){
			$temp = $this->data[$x][$i];
			$this->data[$x][$i] = $this->data[$y][$i];
			$this->data[$y][$i] = $temp;
		}
	}
}

?>
