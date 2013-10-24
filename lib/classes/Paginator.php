<?php

namespace Snilius\Util;

class Paginator {
  private $itemsPerPage;
  private $pagesToDisplay;
  private $totalItems;
  private $totalPages;
  private $page;
  
  /**
   * Paginator
   * @param int $itemsPerPage
   * @param int $itemsToDisplay
   * @param int $totalItems
   */
  public function __construct($itemsPerPage,$pagesToDisplay,$totalItems) {
    $this->itemsPerPage = $itemsPerPage;
    $this->pagesToDisplay = $pagesToDisplay;
    $this->totalItems = $totalItems;
  }
  
  /**
   * Get pagination ui for current page
   * @param int $page
   * @return string
   */
  public function getPagination($page) {
    //for sace of readability, make them shorter
    $itemsPerPage = $this->itemsPerPage;
    $pagesToDisplay = $this->pagesToDisplay;
    $pagesToDisplayMobile = 8;
    $totalItems = $this->totalItems;
    
    $totalPages = ceil($totalItems/$itemsPerPage);
    $startPage = 1;
    $startPageMobile = $startPage;
    
    if($page>($totalPages-($pagesToDisplay/2)))
      $startPage=$totalPages-$pagesToDisplay;
    else if($page>($pagesToDisplay/2))
      $startPage=$page-($pagesToDisplay/2);
    
    if($page>($totalPages-($pagesToDisplayMobile/2)))
      $startPageMobile=$totalPages-$pagesToDisplayMobile;
    else if($page>($pagesToDisplayMobile/2))
      $startPageMobile=$page-($pagesToDisplayMobile/2);
    
    $endPage=$totalPages+1;
    $endPageMobile = $endPage;
    if (($startPage+$pagesToDisplay)<$totalPages)
      $endPage = $startPage+$pagesToDisplay;
    
    if (($startPage+$pagesToDisplayMobile)<$totalPages)
      $endPageMobile = $startPage+$pagesToDisplayMobile;
    
    //save to inatance
    $this->page = $page;
    $this->totalPages = $totalPages;
    
    $ret = '<ul class="pagination hidden-xs">';
    $ret .= '<li><a href="?page=1">&larr;</a></li>';
    $ret .= '<li><a href="'.(($page-1>0)?'?page='.($page-1):'#').'">&laquo;</a></li>';
    for($i=$startPage; $i<$endPage;$i++){
      $ret .= '<li  class="'.(($i==$page)?' active':'').'"><a href="?page='.$i.'">'.$i.'</a></li>';
    }
    $ret .= '<li><a href="'.(($page+1<$totalPages)?'?page='.($page+1):'#').'">&raquo;</a></li>';
    $ret .= '<li><a href="?page='.$totalPages.'">&rarr;</a></li>';
    $ret .= '</ul>';
    
    $ret .= '<ul class="pagination visible-xs">';
    $ret .= '<li><a href="'.(($page-1>0)?'?page='.($page-1):'#').'">&laquo;</a></li>';
    for($i=$startPageMobile; $i<$endPageMobile;$i++){
      $ret .= '<li  class="'.(($i==$page)?'active':'').'"><a href="?page='.$i.'">'.$i.'</a></li>';
    }
    $ret .= '<li><a href="'.(($page+1<$totalPages)?'?page='.($page+1):'#').'">&raquo;</a></li>';
    $ret .= '</ul>';
    
    return $ret;
  }
  
  public function getStart() {
    return ($this->page*$this->itemsPerPage)-$this->itemsPerPage;
  }
}

?>