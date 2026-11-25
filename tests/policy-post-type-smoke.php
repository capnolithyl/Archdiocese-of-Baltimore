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
check($args['has_archive']===false&&$args['rewrite']['slug']==='policy','No automatic CPT archive; single URLs retained');
check(in_array('editor',$args['supports'],true)&&in_array('revisions',$args['supports'],true),'Native editor and revisions');
check($GLOBALS['tax'][0]==='balt_policy_category'&&$GLOBALS['tax'][1]==='balt_policy'&&$GLOBALS['tax'][2]['hierarchical'],'Policy categories');
$q=new WP_Query(['post_type'=>'balt_policy','s'=>'security']);
balt_policy_search_query($q);
check($q->vars['post_status']==='publish'&&$q->vars['has_password']===false,'Only published unprotected policies');
foreach([new WP_Query(['s'=>'security']),new WP_Query(['post_type'=>'post']),new WP_Query(['post_type'=>'balt_policy'],false),new WP_Query(['post_type'=>'balt_policy'],true,false)] as $other){
 $before=$other->vars;balt_policy_search_query($other);check($before===$other->vars,'Unrelated queries unchanged');
}
check($args['hierarchical']===true,'Parent policies must be supported');
function policy_fixture($id,$parent=0,$status='publish',$password=''){return (object)['ID'=>$id,'post_parent'=>$parent,'post_type'=>'balt_policy','post_status'=>$status,'post_password'=>$password];}
$groups=balt_policy_groups([policy_fixture(1),policy_fixture(2,1),policy_fixture(3,2),policy_fixture(4),policy_fixture(5,99),policy_fixture(6,1,'draft'),policy_fixture(7,1,'publish','secret')]);
check(count($groups)===3,'Root and orphan groups');
check(array_map(fn($p)=>$p->ID,$groups[0]['children'])===[2,3],'Nested children ordered, protected records omitted');
check($groups[2]['parent']->ID===5,'Orphan should remain visible');
echo "Policy CPT, taxonomy, grouped index and scoped public search passed\n";

class WP_Term {}
function absint($v):int{return abs((int)$v);}
function get_field($key,$term){return $GLOBALS['selected_page']??0;}
function get_post($id){return $GLOBALS['landing_posts'][$id]??null;}
function get_page_by_path($slug){return $GLOBALS['landing_slugs'][$slug]??null;}
function get_permalink($p){return '/'.$p->slug.'/';}
function landing_page($slug,$status='publish',$password=''){return (object)['post_type'=>'page','post_status'=>$status,'post_password'=>$password,'slug'=>$slug];}
check(balt_policy_directory_url()==='','No broken archive fallback');
$GLOBALS['landing_slugs']=['policies'=>landing_page('policies','draft'),'archdiocese-policies'=>landing_page('archdiocese-policies','publish','secret'),'policy-library'=>landing_page('policy-library')];
check(balt_policy_directory_url()==='/policy-library/','Former archive slug can be a regular page');
$GLOBALS['landing_slugs']['policies']=landing_page('policies');
check(balt_policy_directory_url()==='/policies/','Prefer published policies page');
$GLOBALS['landing_posts']=[10=>landing_page('custom-policy-directory'),11=>landing_page('draft-directory','draft')];
$GLOBALS['selected_page']=10;
check(balt_policy_directory_url(new WP_Term())==='/custom-policy-directory/','Explicit page wins');
$GLOBALS['selected_page']=11;
check(balt_policy_directory_url(new WP_Term())==='/policies/','Invalid selection falls back to published page');
echo "Editable policy directory landing page resolution passed\n";
