<?php namespace ProcessWire;

class ProfilerProArray extends PaginatedArray {
	
	protected $sort = '';
	protected $nameFilter = '';
	
	/**
	 * Per WireArray interface, indicate what's a valid item
	 *
	 * @param Wire $item
	 * @return bool
	 *
	 */
	public function isValidItem($item) {
		return $item instanceof ProfilerProEvent;
	}

	/**
	 * Per WireArray interface, return a blank Field
	 *
	 * @return Field
	 *
	 */
	public function makeBlankItem() {
		return $this->wire(new ProfilerProEvent());
	}
	
	public function getSort() {
		return $this->sort;
	}
	
	public function setSort($sort) {
		$this->sort = $sort;
	}
	
	public function setNameFilter($name) {
		$this->nameFilter = $name;
	}
	
	public function getNameFilter() {
		return $this->nameFilter;
	}
	
}