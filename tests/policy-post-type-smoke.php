<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
function __($s,$d):string{return $s;}
function add_action(...$args):void{}
function register_post_type($type,$args):void{$GLOBALS['type']=[$type,$args];}
function register_taxonomy($tax,$type,$args):void{$GLOBALS['tax']=[$tax,$type,$args];}
function is_admin():bool{return $GLOBALS['admin']??false;}
class WP_Query {
	public function __construct(public array $vars,public bool $main=true,public bool $search=true){}
	public function get($key){return $this->vars[$key]??null;}
	public function set($key,$value):void{$this->vars[$key]=$value;}
	public function is_main_query():bool{return $this->main;}
	public function is_search():bool{return $this->search;}
}
function check($ok,$msg):void{if(!$ok)throw new RuntimeException($msg);}
require dirname(__DIR__).'/theme/inc/policy-post-type.php';
balt_register_policy_post_type();
[$type,$args]=$GLOBALS['type'];
check($type==='balt_policy'&&$args['public']&&$args['show_in_rest']&&!$args['exclude_from_search'],'Public Gutenberg policy type');
check($args['has_archive']==='policy-library'&&$args['rewrite']['slug']==='policy','Separate landing page and archive URLs');
check(in_array('editor',$args['supports'],true)&&in_array('revisions',$args['supports'],true),'Native editor and revisions');
check($GLOBALS['tax'][0]==='balt_policy_category'&&$GLOBALS['tax'][1]==='balt_policy'&&$GLOBALS['tax'][2]['hierarchical'],'Policy categories');
$q=new WP_Query(['post_type'=>'balt_policy','s'=>'security']);
balt_policy_search_query($q);
check($q->vars['post_status']==='publish'&&$q->vars['has_password']===false,'Only published unprotected policies');
foreach([new WP_Query(['s'=>'security']),new WP_Query(['post_type'=>'post']),new WP_Query(['post_type'=>'balt_policy'],false),new WP_Query(['post_type'=>'balt_policy'],true,false)] as $other){
 $before=$other->vars;balt_policy_search_query($other);check($before===$other->vars,'Unrelated queries unchanged');
}
echo "Policy CPT, taxonomy and scoped public search passed\n";
