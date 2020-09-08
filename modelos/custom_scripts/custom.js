"use strict";

function active_menu_link() {
  let path = window.location.pathname;
  let $nav_item = $('a[href="'+path+'"]').first().parents('.nav-item').first();
  $nav_item.addClass('active');
  
  let $nav_item_parent = $nav_item.parents('.nav-item').first();
  if($nav_item_parent){
    $nav_item_parent.addClass("active");
    $nav_item_parent.children('.nav-link').click();
  }
}

active_menu_link();