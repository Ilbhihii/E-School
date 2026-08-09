(function(){
function init(){
const d=window.studentAssignmentPathData||{};
const subject=document.getElementById('assignmentSubject');
const level=document.getElementById('assignmentLevel');
const cls=document.getElementById('assignmentClass');
const clsWrap=document.getElementById('assignmentClassSelectWrap');
const autoCls=document.getElementById('assignmentClassAuto');
const autoInput=document.getElementById('assignmentClassAutoInput');
const slot=document.getElementById('assignmentSlot');
const preview=document.getElementById('assignmentPathPreview');
const pSubject=document.getElementById('assignmentPathSubject');
const pLevel=document.getElementById('assignmentPathLevel');
const pClass=document.getElementById('assignmentPathClass');
const pSlot=document.getElementById('assignmentPathSlot');
if(!subject||!level||!cls||!slot)return;
if(clsWrap)clsWrap.hidden=false;if(autoCls)autoCls.hidden=true;if(autoInput){autoInput.disabled=true;autoInput.value='';}cls.hidden=false;
const add=(s,v,l,sel=false)=>{const o=document.createElement('option');o.value=String(v);o.textContent=l;o.selected=sel;s.appendChild(o)};
const text=s=>s&&s.value&&s.selectedIndex>=0?s.options[s.selectedIndex].textContent.trim():'';
function update(){const complete=subject.value&&level.value&&cls.value&&slot.value;if(preview)preview.hidden=!complete;if(complete){pSubject.textContent=text(subject);pLevel.textContent=text(level);pClass.textContent=text(cls);pSlot.textContent=text(slot)}}
function fillSlots(w=''){slot.innerHTML='';add(slot,'','Choisir un créneau');const opts=(((d.slotsByPath||{})[String(subject.value)]||{})[String(level.value)]||{})[String(cls.value)]||[];opts.forEach(x=>add(slot,x.id,x.code,String(x.id)===String(w)));slot.disabled=!cls.value||opts.length===0;update()}
function fillClasses(w='',ws=''){cls.innerHTML='';add(cls,'','Choisir une classe');const opts=((d.classesBySubjectLevel||{})[String(subject.value)]||{})[String(level.value)]||[];opts.forEach(x=>add(cls,x.id,x.name,String(x.id)===String(w)));cls.disabled=!level.value||opts.length===0;fillSlots(ws);update()}
function fillLevels(w='',wc='',ws=''){level.innerHTML='';add(level,'','Choisir un niveau');const opts=(d.levelsBySubject||{})[String(subject.value)]||[];opts.forEach(x=>add(level,x.id,x.name,String(x.id)===String(w)));level.disabled=!subject.value||opts.length===0;fillClasses(wc,ws);update()}
subject.addEventListener('change',()=>fillLevels());level.addEventListener('change',()=>fillClasses());cls.addEventListener('change',()=>fillSlots());slot.addEventListener('change',update);
if(d.selectedSubjectId){subject.value=String(d.selectedSubjectId)}fillLevels(d.selectedLevelId||'',d.selectedClassId||'',d.selectedSlotId||'');
const dz=document.getElementById('assignmentDropZone'),fi=document.getElementById('assignmentFile'),fn=document.getElementById('assignmentFileName');if(dz&&fi&&fn){const u=f=>{fn.textContent=f?`${f.name} · ${(f.size/(1024*1024)).toFixed(2)} Mo`:'PDF, DOC ou DOCX — maximum 10 Mo'};fi.addEventListener('change',()=>u(fi.files[0]));['dragenter','dragover'].forEach(e=>dz.addEventListener(e,x=>{x.preventDefault();dz.classList.add('dragging')}));['dragleave','drop'].forEach(e=>dz.addEventListener(e,x=>{x.preventDefault();dz.classList.remove('dragging')}));dz.addEventListener('drop',e=>{const fs=e.dataTransfer.files;if(!fs||!fs.length)return;const t=new DataTransfer();t.items.add(fs[0]);fi.files=t.files;u(fs[0])})}
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init,{once:true});else init();
})();
