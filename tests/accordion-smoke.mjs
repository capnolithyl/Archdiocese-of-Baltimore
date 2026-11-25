import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';
const read=p=>readFileSync(p,'utf8');
const code=read('theme/assets/js/accordion-motion.js');
const editor=read('javascript/accordion-editor.js');
assert.ok(editor.includes("props.name !== 'core/accordion'"));
assert.ok(editor.includes('InspectorControls')&&editor.includes('setAttributes({ baltEyebrow: value })')&&editor.includes('setAttributes({ baltHeading: value })'));
let hidden=true, inert=false, animated=false, cb, animation, frames, currentHeight=160, animationActive=false;
const motion={matches:false,addEventListener(){}};
const panel={
 hasAttribute:n=>n==='hidden'?hidden:inert,
 setAttribute:n=>{assert.equal(n,'inert');inert=true;},
 removeAttribute:n=>{assert.equal(n,'inert');inert=false;},
 classList:{add(){animated=true;},remove(){animated=false;}},
 contains:()=>false,
 getBoundingClientRect:()=>({height:animationActive?currentHeight:160}),
 animate:(f,opts)=>{frames=f;assert.equal(opts.duration,260);animationActive=true;animation={cancel(){animationActive=false;this.oncancel?.();}};return animation;},
};
vm.runInNewContext(code,{window:{matchMedia:()=>motion},document:{readyState:'complete',querySelectorAll:()=>[panel],activeElement:null},MutationObserver:class{constructor(fn){cb=fn;}observe(target,opts){assert.equal(target,panel);assert.deepEqual(Array.from(opts.attributeFilter),['hidden']);}}});
hidden=false;cb();assert.equal(frames[0].height,'0px');assert.equal(frames[1].height,'160px');assert.equal(animated,true);animation.onfinish();assert.equal(animated,false);
hidden=true;cb();assert.equal(frames[0].height,'160px');assert.equal(frames[1].height,'0px');assert.equal(inert,true);
currentHeight=70;hidden=false;cb();assert.equal(frames[0].height,'70px');assert.equal(inert,false);animation.onfinish();assert.equal(animated,false);
motion.matches=true;hidden=true;cb();assert.equal(animated,false);assert.equal(inert,false);
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['balt-accordion-section','balt-accordion-animating','900px','f5f3ee'])assert.ok(css.includes(token),p+' missing '+token);
 assert.ok(/(?:260ms|0?\.26s)/.test(css),p+' missing 260ms transition duration');
}
console.log('Accordion sidebar, motion state, reduced motion and compiled styles passed');
