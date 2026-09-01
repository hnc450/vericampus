import { useState } from 'react'
import { Link } from 'react-router-dom'
import { motion } from 'framer-motion'
import { LogIn, Mail, Lock, Eye, EyeOff, ShieldCheck, Building2, GraduationCap, ArrowRight, Sparkles, Check } from 'lucide-react'
import { VeriCampusLogo } from '../components/VeriCampusLogo.jsx'

const IMG_CAMPUS_NIGHT = "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=900&q=80"
const IMG_AVATAR = "https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80"
const IMG_AVATAR2 = "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80"
const IMG_FORM_HEADER = "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80"

export default function Connexion({ showToast }){
  const [role, setRole] = useState('employeur')
  const [showPwd, setShowPwd] = useState(false)
  const [form, setForm] = useState({ email:'', password:'' })
  const [loading, setLoading] = useState(false)

  const submit = (e)=>{
    e.preventDefault()
    if(!form.email || !form.password){ showToast('Veuillez remplir tous les champs.'); return }
    setLoading(true)
    setTimeout(()=>{ setLoading(false); showToast(`Connexion réussie — Bienvenue (${role}) — démo VeriCampus`); }, 1300)
  }

  const fillDemo = (r)=>{
    setRole(r)
    if(r==='employeur') setForm({email:'rh@socogenerale.cd', password:'demo1234'})
    if(r==='universite') setForm({email:'scolarite@unikin.ac.cd', password:'demo1234'})
    if(r==='etudiant') setForm({email:'etudiant@unikin.ac.cd', password:'demo1234'})
  }

  return (
    <div className="grid lg:grid-cols-[1.05fr_0.95fr] gap-6 items-stretch">
      <motion.div initial={{opacity:0,x:-12}} animate={{opacity:1,x:0}} transition={{duration:0.55}} className="rounded-[28px] bg-[#1B1D3B] text-white p-7 md:p-10 flex flex-col relative overflow-hidden border border-[#2A2D5C] hover-lift">
        <img src={IMG_CAMPUS_NIGHT} alt="Campus VeriCampus" className="absolute inset-0 w-full h-full object-cover opacity-45 block"/>
        <div className="absolute inset-0 bg-gradient-to-t from-[#1B1D3B] via-[#1B1D3B]/55 to-[#1B1D3B]/25"/>
        <div className="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#C9A86A]/20 blur-3xl"/>
        <div className="relative">
          {/* Logo VeriCampus — désormais visible */}
          <div className="inline-flex items-center gap-3 bg-white rounded-2xl px-4 py-2.5 shadow-lg">
            <VeriCampusLogo size={36} />
          </div>
          <div className="mt-4 inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 px-3 py-1.5 text-xs backdrop-blur"><ShieldCheck size={14} className="text-[#C9A86A]"/> Accès sécurisé • Chiffrement AES-256</div>
          <h1 className="serif mt-6 text-[34px] md:text-[40px] font-bold leading-[0.95]">Ravie de vous<br/><span className="text-[#C9A86A]">revoir.</span></h1>
          <p className="mt-4 text-white/70 text-[14px] leading-relaxed">Connectez-vous à VeriCampus — employeur, université ou diplômé. Démo front-only, aucune donnée stockée.</p>

          <div className="mt-8 grid gap-3">
            {[
              {role:'employeur', icon:Building2, title:'Portail Employeur', desc:'Vérifications illimitées, API RH', demo:'rh@socogenerale.cd / demo1234'},
              {role:'universite', icon:GraduationCap, title:'Dashboard Université', desc:'Gestion palmarès & ancrages', demo:'scolarite@unikin.ac.cd / demo1234'},
              {role:'etudiant', icon:ShieldCheck, title:'Espace Diplômé', desc:'Partage badge QR vérifié', demo:'etudiant@unikin.ac.cd / demo1234'},
            ].map((c,i)=>(
              <motion.button key={c.role} initial={{opacity:0,y:8}} animate={{opacity:1,y:0}} transition={{delay:i*0.08}} whileHover={{scale:1.01}} whileTap={{scale:0.99}} onClick={()=>fillDemo(c.role)} className={`text-left rounded-2xl border p-4 flex gap-3 transition ${role===c.role ? 'bg-white text-[#1B1D3B] border-white shadow' : 'bg-white/10 border-white/15 text-white hover:bg-white/15 backdrop-blur'}`}>
                <c.icon size={20} className={role===c.role?'text-[#7A1C1C]':'text-[#C9A86A]'}/>
                <span className="flex-1"><span className="block text-sm font-bold">{c.title}</span><span className={`block text-xs ${role===c.role?'text-[#6B6575]':'text-white/60'}`}>{c.desc}</span><span className="mono text-[11px] mt-1 inline-block bg-black/10 px-2 py-1 rounded-full">{c.demo}</span></span>
                {role===c.role && <Check size={16} className="text-[#1B4A3A] mt-1 animate-scaleIn"/>}
              </motion.button>
            ))}
          </div>

          <div className="mt-8 rounded-2xl bg-white/10 border border-white/15 p-4 backdrop-blur">
            <div className="flex items-center gap-2 text-sm font-semibold"><Sparkles size={16} className="text-[#C9A86A]"/> Pourquoi VeriCampus ?</div>
            <ul className="mt-2 grid gap-1.5 text-xs text-white/70 list-disc pl-5">
              <li>Historique complet & audit trail horodaté</li>
              <li>Export PDF/CSV & badge LinkedIn</li>
              <li>SSO & rôles équipes RH / scolarité</li>
            </ul>
          </div>
        </div>
        <div className="relative mt-auto pt-8 flex items-center gap-3 text-xs text-white/70">
          <div className="flex -space-x-2">
            <img src={IMG_AVATAR} alt="support VeriCampus" className="h-8 w-8 rounded-full object-cover border-2 border-[#1B1D3B] shadow block"/>
            <img src={IMG_AVATAR2} alt="support" className="h-8 w-8 rounded-full object-cover border-2 border-[#1B1D3B] shadow block"/>
            <img src={IMG_FORM_HEADER} alt="étudiant" className="h-8 w-8 rounded-full object-cover border-2 border-[#1B1D3B] shadow block"/>
          </div>
          <span>Besoin d'aide ? <a href="mailto:contact@vericampus.cd" className="underline hover:text-white">contact@vericampus.cd</a></span>
        </div>
      </motion.div>

      <motion.div initial={{opacity:0,x:12}} animate={{opacity:1,x:0}} transition={{duration:0.55, delay:0.1}} className="rounded-[28px] bg-white border border-[#E9DDCB] shadow-sm flex flex-col hover-lift overflow-hidden">
        {/* Image d’en-tête — désormais visible */}
        <div className="relative h-28 -mx-6 -mt-6 md:-mx-8 md:-mt-8 mb-6 overflow-hidden border-b border-[#E9DDCB]">
          <img src={IMG_FORM_HEADER} alt="Connexion VeriCampus - étudiants" className="absolute inset-0 w-full h-full object-cover block"/>
          <div className="absolute inset-0 bg-gradient-to-r from-[#1B1D3B]/80 via-[#1B1D3B]/40 to-transparent"/>
          <div className="absolute inset-0 flex items-end p-4 md:p-6">
            <div className="bg-white/95 backdrop-blur rounded-2xl px-4 py-2.5 flex items-center gap-3 shadow">
              <VeriCampusLogo size={32} withText={false} />
              <div className="text-left">
                <div className="text-xs font-bold tracking-widest uppercase text-[#1B1D3B]">VeriCampus</div>
                <div className="text-[11px] text-[#6B6575]">Connexion sécurisée • SSO disponible</div>
              </div>
              <div className="ml-2 h-2 w-2 rounded-full bg-[#1B6B4A] animate-pulse"/>
            </div>
          </div>
        </div>
        <div className="flex items-center justify-between">
          <h2 className="serif text-[22px] font-bold text-[#1B1D3B]">Connexion VeriCampus</h2>
          <Link to="/verification" className="text-xs font-semibold text-[#7A1C1C] hover:underline inline-flex items-center gap-1">Sans compte <ArrowRight size={12}/></Link>
        </div>
        <p className="text-sm text-[#6B6575] mt-1">Accédez à votre espace sécurisé.</p>

        <div className="mt-6 grid grid-cols-3 gap-2 p-1 rounded-full bg-[#F2EDE6] border border-[#E9DDCB]">
          {[
            {id:'employeur', label:'Employeur'},
            {id:'universite', label:'Université'},
            {id:'etudiant', label:'Diplômé'},
          ].map(r=>(
            <button key={r.id} onClick={()=>setRole(r.id)} className={`rounded-full px-3 py-2 text-xs font-bold transition ${role===r.id ? 'bg-[#1B1D3B] text-white shadow' : 'text-[#6B6575] hover:bg-white'}`}>{r.label}</button>
          ))}
        </div>

        <form onSubmit={submit} className="mt-6 grid gap-4">
          <div>
            <label className="text-xs font-bold tracking-widest uppercase text-[#6B6575]">Email professionnel *</label>
            <div className="mt-1.5 relative">
              <Mail size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9A96A6]"/>
              <input type="email" value={form.email} onChange={e=>setForm({...form,email:e.target.value})} placeholder={role==='universite' ? 'scolarite@unikin.ac.cd' : role==='etudiant' ? 'etudiant@unikin.ac.cd' : 'rh@entreprise.cd'} className="w-full rounded-2xl border border-[#E9DDCB] bg-[#FFFBF5] pl-10 pr-4 py-3.5 text-sm focus:bg-white focus:border-[#C9A86A] focus:ring-4 focus:ring-[#C9A86A]/20 outline-none"/>
            </div>
          </div>
          <div>
            <div className="flex items-center justify-between"><label className="text-xs font-bold tracking-widest uppercase text-[#6B6575]">Mot de passe *</label><a onClick={()=>showToast('Lien envoyé (simulé)')} className="text-xs font-semibold text-[#7A1C1C] hover:underline cursor-pointer">Oublié ?</a></div>
            <div className="mt-1.5 relative">
              <Lock size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9A96A6]"/>
              <input type={showPwd?'text':'password'} value={form.password} onChange={e=>setForm({...form,password:e.target.value})} placeholder="••••••••" className="w-full rounded-2xl border border-[#E9DDCB] bg-[#FFFBF5] pl-10 pr-10 py-3.5 text-sm focus:bg-white focus:border-[#C9A86A] focus:ring-4 focus:ring-[#C9A86A]/20 outline-none"/>
              <button type="button" onClick={()=>setShowPwd(v=>!v)} className="absolute right-3 top-1/2 -translate-y-1/2 h-8 w-8 rounded-full bg-white border border-[#E9DDCB] flex items-center justify-center">{showPwd ? <EyeOff size={14}/> : <Eye size={14}/>}</button>
            </div>
          </div>

          <label className="flex items-center gap-2 text-xs text-[#6B6575]"><input type="checkbox" className="rounded border-[#E9DDCB]"/> Rester connecté</label>

          <motion.button whileHover={{scale:1.01}} whileTap={{scale:0.98}} type="submit" disabled={loading} className="mt-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-[#1B1D3B] text-white py-4 text-sm font-bold hover:bg-[#23265a] disabled:opacity-60 shadow">
            {loading ? <><span className="h-4 w-4 rounded-full border-2 border-white/30 border-t-white animate-spin"/> Connexion…</> : <><LogIn size={18} className="text-[#C9A86A]"/> Se connecter <ArrowRight size={16}/></>}
          </motion.button>

          <div className="relative py-2"><div className="absolute inset-0 flex items-center"><div className="w-full border-t border-[#E9DDCB]"/></div><div className="relative flex justify-center"><span className="bg-white px-3 text-xs text-[#6B6575]">ou</span></div></div>

          <button type="button" onClick={()=>showToast('SSO bientôt disponible')} className="inline-flex items-center justify-center gap-2 rounded-2xl bg-white border border-[#E9DDCB] py-3.5 text-sm font-semibold hover:bg-[#F2EDE6]">Continuer avec SSO Entreprise</button>
          <p className="text-center text-xs text-[#6B6575]">Pas de compte ? <Link to="/verification" className="font-bold text-[#7A1C1C] hover:underline">Vérifier sans créer de compte</Link></p>
        </form>

        <div className="mt-auto pt-6 flex items-center justify-center gap-2 text-[11px] text-[#9A96A6]"><Lock size={12}/> Démo 100% locale • Aucune donnée envoyée</div>
      </motion.div>
    </div>
  )
}
