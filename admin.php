<?php
// +-----------------------------------------------------------------------+
// |                               admin.php                               |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
define('IN_ADMIN', true);
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

//--------------------------------------- validating page and creation of title
$page_valide = false;
$title = '';
if (isset( $_GET['page'] ))
switch ( $_GET['page'] )
{
 case 'user_list':
   $title = $lang['title_liste_users'];   $page_valide = true; break;
 case 'user_modify':
   $title = $lang['title_modify'];        $page_valide = true; break;
 case 'user_search':
  /* if ( !is_numeric( $_GET['user_id'] ) ) $_GET['user_id'] = -1;
   $query = 'SELECT status,username';
   $query.= ' FROM '.USERS_TABLE;
   $query.= ' WHERE id = '.$_GET['user_id'];
   $query.= ';';
   $result = mysql_query( $query );
   if ( mysql_num_rows( $result ) > 0 )
   {
     $row = mysql_fetch_array( $result );
     $page['user_status']   = $row['status'];
     if ( $row['username'] == 'guest' ) $row['username'] = $lang['guest'];
     $page['user_username'] = $row['username'];
     $page_valide = true;
     $title = $lang['title_user_perm'].' "'.$page['user_username'].'"';
   }
   else
   {
     $page_valide = false;
   }*/
   $title = $lang['title_user_perm'];
   //.' '.$_POST['username'];
   $page_valide = true; break;
 case 'group_list' :
   $title = $lang['title_groups'];        $page_valide = true; break;
 case 'group_perm' :
   if ( !is_numeric( $_GET['group_id'] ) ) $_GET['group_id'] = -1;
   $query = 'SELECT name FROM '.GROUPS_TABLE;
   $query.= ' WHERE id = '.$_GET['group_id'];
   $query.= ';';
   $result = mysql_query( $query );
   if ( mysql_num_rows( $result ) > 0 )
   {
     $row = mysql_fetch_array( $result );
     $title = $lang['title_group_perm'].' "'.$row['name'].'"';
     $page_valide = true;
   }
   else
   {
     $page_valide = false;
   }
   break;
 case 'stats':
   $title = $lang['title_history'];       $page_valide = true; break;
 case 'update':
   $title = $lang['title_update'];        $page_valide = true; break;
 case 'configuration':
   $title = $lang['title_configuration']; $page_valide = true; break;
 case 'help':
   $title = $lang['title_instructions'];  $page_valide = true; break;
 case 'cat_perm':
   $title = $lang['title_cat_perm'];
   if ( isset( $_GET['cat_id'] ) )
   {
     check_cat_id( $_GET['cat_id'] );
     if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
     {
       $result = get_cat_info( $page['cat'] );
       $name = get_cat_display_name( $result['name'],' &gt; ', '' );
       $title.= ' "'.$name.'"';
     }
   }
   $page_valide = true;
   break;
 case 'cat_list':
   $title = $lang['title_categories'];    $page_valide = true; break;
 case 'cat_modify':
   $title = $lang['title_edit_cat'];      $page_valide = true; break;
 case 'infos_images':
   $title = $lang['title_info_images'];   $page_valide = true; break;
 case 'waiting':
   $title = $lang['title_waiting'];       $page_valide = true; break;
 case 'thumbnail':
   $title = $lang['title_thumbnails'];
   if ( isset( $_GET['dir'] ) )
   {
     $title.= ' '.$lang['title_thumbnails_2'].' <span class="titreImg">';
     // $_GET['dir'] contains :
     // ./galleries/vieux_lyon ou
     // ./galleries/vieux_lyon/visite ou
     // ./galleries/vieux_lyon/visite/truc ...
     $dir = explode( "/", $_GET['dir'] );
     $title.= $dir[2];
     for ( $i = 2; $i < sizeof( $dir ) - 1; $i++ )
     {
       $title.= ' &gt; '.$dir[$i+1];
     }
     $title.= "</span>";
   }
   $page_valide = true;
   break;
 case 'comments' :
   $title = $lang['title_comments'];
   $page_valide = true;
   break;
 case 'picture_modify' :
   $title = $lang['title_picmod'];
   $page_valide = true;
   break;
 default:
   $title = $lang['title_default']; break;
}
if ( $title == '' ) $title = $lang['title_default'];

// waiting
$query = 'SELECT id FROM '.WAITING_TABLE;
$query.= " WHERE validated='false'";
$query.= ';';
$result = mysql_query( $query );
$nb_waiting = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_waiting =  ' [ '.mysql_num_rows( $result ).' ]';
}
// comments
$query = 'SELECT id FROM '.COMMENTS_TABLE;
$query.= " WHERE validated='false'";
$query.= ';';
$result = mysql_query( $query );
$nb_comments = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_comments =  ' [ '.mysql_num_rows( $result ).' ]';
}

$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
//----------------------------------------------------- template initialization
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->set_filenames( array('admin'=>'admin.tpl') );

$template->assign_vars(array(
  'L_CONFIG'=>$lang['menu_config'],
  'L_USERS'=>$lang['menu_users'],
  'L_GROUPS'=>$lang['menu_groups'],
  'L_CATEGORIES'=>$lang['menu_categories'],
  'L_WAITING'=>$lang['menu_waiting'].$nb_waiting,
  'L_COMMENTS'=>$lang['menu_comments'].$nb_comments,
  'L_UPDATE'=>$lang['menu_update'],
  'L_THUMBNAILS'=>$lang['menu_thumbnails'],
  'L_HISTORY'=>$lang['menu_history'],
  'L_FAQ'=>$lang['menu_instructions'],
  'L_RETURN'=>$lang['menu_back'],
  
  'U_CONFIG'=>add_session_id($link_start.'configuration' ),
  'U_USERS'=>add_session_id($link_start.'user_search' ),
  'U_GROUPS'=>add_session_id($link_start.'group_list' ),
  'U_CATEGORIES'=>add_session_id($link_start.'cat_list' ),
  'U_WAITING'=>add_session_id($link_start.'waiting' ),
  'U_COMMENTS'=>add_session_id($link_start.'comments' ),
  'U_UPDATE'=>add_session_id($link_start.'update' ),
  'U_THUMBNAILS'=>add_session_id($link_start.'thumbnail' ),
  'U_HISTORY'=>add_session_id($link_start.'stats' ),
  'U_FAQ'=>add_session_id($link_start.'help' ),
  'U_RETURN'=>add_session_id(PHPWG_ROOT_PATH.'category.php')
  ));

//--------------------------------------------------------------------- summary
$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
//------------------------------------------------------------- content display
if ( $page_valide )
{
  if ($_GET['page']=='comments') include ( PHPWG_ROOT_PATH.'comments.php');
  else include ( PHPWG_ROOT_PATH.'admin/'.$_GET['page'].'.php' );
}
else
{
  $template->assign_vars(array ('ADMIN_CONTENT'=> '<div style="text-align:center">'.$lang['default_message'].'</div>') );
}
$template->pparse('admin');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
