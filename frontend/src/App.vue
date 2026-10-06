<script setup>
import { ref, reactive, computed, h, onMounted, onBeforeUnmount } from 'vue'

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1'
const DOCS_URL = 'http://localhost:8000/docs/api'

/* ------------------------------------------------------------------
 * Carousel Hero Béninois (3 Images Typiquement Béninoises)
 * ------------------------------------------------------------------ */
const carouselSlides = [
  {
    image: '/images/slide1.jpg',
    badge: 'République du Bénin · e-Services',
    title: 'Centre de Services e-Gouvernement du Bénin',
    subtitle: 'La dématérialisation intégrale des actes d\'état civil et pièces administratives à portée de main.',
  },
  {
    image: '/images/slide2.jpg',
    badge: 'ASIN Bénin · Innovation',
    title: 'Cotonou Ville Connectée & Smart Nation',
    subtitle: 'L\'excellence numérique au service du développement et de la modernisation des services publics.',
  },
  {
    image: '/images/slide3.jpg',
    badge: 'Citoyenneté & Simplicité',
    title: 'Vos Démarches Numériques Simplifiées',
    subtitle: 'Obtenez vos actes de naissance, casiers judiciaires et certificats de résidence rapidement.',
  },
]

const currentSlide = ref(0)
let carouselTimer = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % carouselSlides.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + carouselSlides.length) % carouselSlides.length
}

const startCarousel = () => {
  stopCarousel()
  carouselTimer = setInterval(nextSlide, 5000)
}

const stopCarousel = () => {
  if (carouselTimer) clearInterval(carouselTimer)
}

/* ------------------------------------------------------------------
 * Icônes SVG (Heroicons outline) – aucun emoji dans l'interface
 * ------------------------------------------------------------------ */
const ICONS = {
  speaker: 'M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z',
  stop: 'M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z',
  search: 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
  document: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
  check: 'm4.5 12.75 6 6 9-13.5',
  close: 'M6 18 18 6M6 6l12 12',
  arrowLeft: 'M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18',
  arrowRight: 'M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3',
  lock: 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
  shield: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
  inbox: 'M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z',
  clock: 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
  checkCircle: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
  xCircle: 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
  warning: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
  refresh: 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
  logout: 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9',
  menu: 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
  user: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
  globe: 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a15.3 15.3 0 0 1-4-9 15.3 15.3 0 0 1 4-9 15.3 15.3 0 0 1 4 9 15.3 15.3 0 0 1-4 9Zm-8.6-9h17.2',
  chevronLeft: 'M15.75 19.5 8.25 12l7.5-7.5',
  chevronRight: 'M8.25 4.5l7.5 7.5-7.5 7.5',
  printer: 'M6.75 6a2.25 2.25 0 0 1 2.25-2.25h6a2.25 2.25 0 0 1 2.25 2.25v2.25H6.75V6ZM3.75 15.75A2.25 2.25 0 0 1 1.5 13.5v-3a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 10.5v3a2.25 2.25 0 0 1-2.25 2.25H3.75ZM16.5 18H7.5A2.25 2.25 0 0 1 5.25 15.75v-3h13.5v3A2.25 2.25 0 0 1 16.5 18Z',
  clipboard: 'M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v9.25c0 .621-.504 1.125-1.125 1.125Z',
}

const Icon = (props) =>
  h('svg', {
    xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24',
    'stroke-width': 1.6, stroke: 'currentColor', 'aria-hidden': 'true',
  }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: ICONS[props.name] || ICONS.document })])
Icon.props = ['name']

/* ------------------------------------------------------------------
 * Routage par hash : "/" = portail public, "#/agent" = espace agent
 * ------------------------------------------------------------------ */
const currentRoute = ref(window.location.hash || '#/')
const onHashChange = () => { currentRoute.value = window.location.hash || '#/' }
const isAgentRoute = computed(() => currentRoute.value.startsWith('#/agent'))

/* ------------------------------------------------------------------
 * Référentiels
 * ------------------------------------------------------------------ */
const TYPES_ACTES = [
  { value: 'acte de naissance', label: 'Acte de naissance' },
  { value: 'casier judiciaire', label: 'Casier judiciaire' },
  { value: 'certificat de résidence', label: 'Certificat de résidence' },
]

const STATUTS = {
  'déposée': { label: 'Déposée', badge: 'bg-sky-50 text-sky-700 ring-sky-600/20', dot: 'bg-sky-500', icon: 'inbox', accent: 'text-sky-600 bg-sky-50' },
  'en cours de traitement': { label: 'En cours de traitement', badge: 'bg-amber-50 text-amber-700 ring-amber-600/20', dot: 'bg-amber-500', icon: 'clock', accent: 'text-amber-600 bg-amber-50' },
  'validée': { label: 'Validée', badge: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20', dot: 'bg-emerald-500', icon: 'checkCircle', accent: 'text-emerald-600 bg-emerald-50' },
  'rejetée': { label: 'Rejetée', badge: 'bg-rose-50 text-rose-700 ring-rose-600/20', dot: 'bg-rose-500', icon: 'xCircle', accent: 'text-rose-600 bg-rose-50' },
}
const statutMeta = (s) => STATUTS[s] || { label: s, badge: 'bg-slate-50 text-slate-700 ring-slate-600/20', dot: 'bg-slate-400', icon: 'document', accent: 'text-slate-600 bg-slate-50' }
const formatDate = (d) => new Date(d).toLocaleString('fr-FR', { dateStyle: 'medium', timeStyle: 'short' })

/* ------------------------------------------------------------------
 * Synthèse vocale locale (Web Speech API)
 * ------------------------------------------------------------------ */
const speakingKey = ref(null)
const speak = (texte, key = null) => {
  if (!('speechSynthesis' in window)) return
  window.speechSynthesis.cancel()
  if (key && speakingKey.value === key) { speakingKey.value = null; return }
  const u = new SpeechSynthesisUtterance(texte)
  u.lang = 'fr-FR'
  u.rate = 0.95
  u.onend = u.onerror = () => { speakingKey.value = null }
  speakingKey.value = key
  window.speechSynthesis.speak(u)
}
const speakGuide = () => speak(
  "Bienvenue sur le portail de l'Agence des Systèmes d'Information et du Numérique du Bénin. " +
  "Pour consulter votre tableau de bord citoyen, saisissez votre NPI à dix chiffres. " +
  "Pour déposer une nouvelle demande, remplissez le formulaire de dépôt.",
  'guide'
)
const speakDemande = (d) => {
  let txt = `Demande de ${d.type_acte}, ${d.nombre_copies} exemplaire${d.nombre_copies > 1 ? 's' : ''}. Statut : ${d.statut}.`
  if (d.statut === 'rejetée' && d.motif_rejet) txt += ` Motif du rejet : ${d.motif_rejet}.`
  speak(txt, d.reference)
}

/* ------------------------------------------------------------------
 * Portail public : recherche par NPI & Tableau de Bord Citoyen
 * ------------------------------------------------------------------ */
const rechercheNpi = ref('')
const filterStatutUsager = ref('')
const demandesUsager = ref([])
const totalDemandesUsager = ref(0)
const loadingSearch = ref(false)
const searchErrorMessage = ref('')
const rechercheFaite = ref(false)

// Bonus : Copie Référence & Impression Récépissé Officiel
const copiedRef = ref(null)
const selectedRecepisse = ref(null)
const showRecepisseModal = ref(false)

const copyReference = (refVal) => {
  navigator.clipboard.writeText(refVal)
  copiedRef.value = refVal
  setTimeout(() => { if (copiedRef.value === refVal) copiedRef.value = null }, 2500)
}

const openRecepisse = (d) => {
  selectedRecepisse.value = d
  showRecepisseModal.value = true
}

const triggerPrint = () => {
  window.print()
}

const extractList = (body) => (Array.isArray(body.data) ? body.data : body.data?.data || [])

// Statistiques du Citoyen Connecté par NPI
const citoyenStats = computed(() => {
  const total = demandesUsager.value.length
  const deposees = demandesUsager.value.filter(d => d.statut === 'déposée').length
  const enCours = demandesUsager.value.filter(d => d.statut === 'en cours de traitement').length
  const validees = demandesUsager.value.filter(d => d.statut === 'validée').length
  const rejetees = demandesUsager.value.filter(d => d.statut === 'rejetée').length
  return { total, deposees, enCours, validees, rejetees }
})

const chargerDemandesUsager = async () => {
  searchErrorMessage.value = ''
  if (!/^[0-9]{10}$/.test(rechercheNpi.value)) {
    searchErrorMessage.value = 'Le NPI doit comporter exactement 10 chiffres.'
    demandesUsager.value = []
    speak(searchErrorMessage.value)
    return
  }
  loadingSearch.value = true
  try {
    const qs = filterStatutUsager.value ? `?statut=${encodeURIComponent(filterStatutUsager.value)}` : ''
    const res = await fetch(`${API_BASE}/usagers/${rechercheNpi.value}/demandes${qs}`, { headers: { Accept: 'application/json' } })
    const body = await res.json()
    rechercheFaite.value = true
    if (res.ok && body.success) {
      demandesUsager.value = extractList(body)
      totalDemandesUsager.value = body.data?.total ?? demandesUsager.value.length
      speak(`Tableau de bord chargé. ${totalDemandesUsager.value} demande${totalDemandesUsager.value > 1 ? 's' : ''} répertoriée${totalDemandesUsager.value > 1 ? 's' : ''} pour votre NPI.`)
    } else {
      demandesUsager.value = []
      searchErrorMessage.value = body.message || 'Aucune demande trouvée.'
      speak(searchErrorMessage.value)
    }
  } catch {
    searchErrorMessage.value = 'Le service est momentanément indisponible. Veuillez réessayer.'
    speak(searchErrorMessage.value)
  } finally {
    loadingSearch.value = false
  }
}

/* ------------------------------------------------------------------
 * Portail public : dépôt de demande
 * ------------------------------------------------------------------ */
const nouveauDepot = reactive({ npi: '', type_acte: 'acte de naissance', nombre_copies: 1 })
const formErrors = reactive({})
const formSuccess = ref(null)
const formErrorMessage = ref('')
const submittingForm = ref(false)

const deposerDemande = async () => {
  Object.keys(formErrors).forEach((k) => delete formErrors[k])
  formSuccess.value = null
  formErrorMessage.value = ''

  if (!/^[0-9]{10}$/.test(nouveauDepot.npi)) {
    formErrors.npi = ['Le NPI doit comporter exactement 10 chiffres.']
    speak(formErrors.npi[0])
    return
  }
  submittingForm.value = true
  try {
    const res = await fetch(`${API_BASE}/demandes`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(nouveauDepot),
    })
    const body = await res.json()
    if (res.status === 201 && body.success) {
      formSuccess.value = { reference: body.data.reference, npi: nouveauDepot.npi }
      speak('Votre demande a été enregistrée avec succès. Elle est au statut déposée.')
      rechercheNpi.value = nouveauDepot.npi
      chargerDemandesUsager()
      nouveauDepot.npi = ''
      nouveauDepot.nombre_copies = 1
    } else {
      formErrorMessage.value = body.message || 'Les informations saisies sont invalides.'
      if (body.errors) Object.assign(formErrors, body.errors)
      speak(formErrorMessage.value)
    }
  } catch {
    formErrorMessage.value = 'Le service est momentanément indisponible. Veuillez réessayer.'
    speak(formErrorMessage.value)
  } finally {
    submittingForm.value = false
  }
}

/* ------------------------------------------------------------------
 * Espace agent & Dashboard PRO (Soft UI Style)
 * ------------------------------------------------------------------ */
const agentAuthenticated = ref(false)
const agentCodeInput = ref('')
const agentAuthError = ref('')
const agentDemandes = ref([])
const agentFilterStatut = ref('')
const loadingAgent = ref(false)
const agentNotice = ref(null)
const statsData = reactive({ 'déposée': 0, 'en cours de traitement': 0, 'validée': 0, 'rejetée': 0 })

const sidebarOpen = ref(false)
const agentView = ref('dashboard') // 'dashboard' | 'demandes'
const agentSearch = ref('')

const AGENT_NAV = [
  { key: 'dashboard', label: 'Tableau de bord', icon: 'inbox' },
  { key: 'demandes', label: 'Demandes', icon: 'document' },
]

const currentNavLabel = computed(() => AGENT_NAV.find(n => n.key === agentView.value)?.label || 'Tableau de bord')
const goTo = (view) => { agentView.value = view; sidebarOpen.value = false }

const showRejetModal = ref(false)
const selectedDemandeRejet = ref(null)
const motifRejetInput = ref('')
const rejetErrorMessage = ref('')

let noticeTimer = null
const notify = (type, text) => {
  agentNotice.value = { type, text }
  clearTimeout(noticeTimer)
  noticeTimer = setTimeout(() => { agentNotice.value = null }, 4000)
}

const authenticateAgent = () => {
  agentAuthError.value = ''
  if (agentCodeInput.value.trim() === 'AGENT2026') {
    agentAuthenticated.value = true
    agentCodeInput.value = ''
    refreshAgent()
  } else {
    agentAuthError.value = "Code d'habilitation invalide."
  }
}

const logoutAgent = () => {
  agentAuthenticated.value = false
  agentDemandes.value = []
}

const loadAgentDemandes = async () => {
  loadingAgent.value = true
  try {
    const qs = agentFilterStatut.value ? `?statut=${encodeURIComponent(agentFilterStatut.value)}` : ''
    const res = await fetch(`${API_BASE}/demandes${qs}`, { headers: { Accept: 'application/json' } })
    const body = await res.json()
    if (res.ok && body.success) agentDemandes.value = extractList(body)
  } catch {
    notify('error', 'Impossible de charger les demandes.')
  } finally {
    loadingAgent.value = false
  }
}

const loadStats = async () => {
  try {
    const res = await fetch(`${API_BASE}/demandes/stats`, { headers: { Accept: 'application/json' } })
    const body = await res.json()
    if (res.ok && body.success) Object.assign(statsData, body.data)
  } catch { /* silencieux */ }
}

const refreshAgent = () => { loadAgentDemandes(); loadStats() }

const updateStatutAgent = async (demande, nouveauStatut, motif = null) => {
  try {
    const payload = { statut: nouveauStatut }
    if (motif) payload.motif_rejet = motif
    const res = await fetch(`${API_BASE}/demandes/${demande.reference}/statut`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(payload),
    })
    const body = await res.json()
    if (res.ok && body.success) {
      notify('success', `Demande passée au statut « ${statutMeta(nouveauStatut).label} ».`)
      refreshAgent()
      return true
    }
    notify('error', body.message || 'Changement de statut refusé.')
  } catch {
    notify('error', 'Erreur réseau lors de la mise à jour.')
  }
  return false
}

const openRejetModal = (demande) => {
  selectedDemandeRejet.value = demande
  motifRejetInput.value = ''
  rejetErrorMessage.value = ''
  showRejetModal.value = true
}

const confirmRejet = async () => {
  if (motifRejetInput.value.trim().length < 5) {
    rejetErrorMessage.value = 'Le motif doit contenir au moins 5 caractères.'
    return
  }
  const ok = await updateStatutAgent(selectedDemandeRejet.value, 'rejetée', motifRejetInput.value.trim())
  if (ok) showRejetModal.value = false
}

const totalDemandes = computed(() => agentDemandes.value.length)

const filteredAgentDemandes = computed(() => {
  let list = agentDemandes.value
  if (agentSearch.value.trim()) {
    const q = agentSearch.value.trim().toLowerCase()
    list = list.filter(d => d.npi.toLowerCase().includes(q) || d.reference.toLowerCase().includes(q))
  }
  return list
})

const recentDemandes = computed(() => agentDemandes.value.slice(0, 5))

const statCards = computed(() => {
  const tot = totalDemandes.value || 1
  return Object.keys(STATUTS).map((key) => {
    const val = statsData[key] || 0
    return {
      key,
      ...STATUTS[key],
      value: val,
      share: Math.round((val / tot) * 100)
    }
  })
})

const statusBars = computed(() => {
  const keys = Object.keys(STATUTS)
  const max = Math.max(...keys.map(k => statsData[k] || 0), 1)
  const shortLabels = { 'déposée': 'Déposée', 'en cours de traitement': 'En cours', 'validée': 'Validée', 'rejetée': 'Rejetée' }
  return keys.map(k => ({
    key: k,
    label: STATUTS[k].label,
    short: shortLabels[k] || k,
    value: statsData[k] || 0,
    height: Math.round(((statsData[k] || 0) / max) * 100)
  }))
})

const weekActivity = computed(() => {
  const now = new Date()
  const days = []
  for (let i = 6; i >= 0; i--) {
    const d = new Date(now)
    d.setDate(d.getDate() - i)
    const dayStr = d.toISOString().split('T')[0]
    const label = d.toLocaleDateString('fr-FR', { weekday: 'short' })
    const count = agentDemandes.value.filter(item => item.created_at && item.created_at.startsWith(dayStr)).length
    days.push({ key: dayStr, label, count })
  }
  const counts = days.map(d => d.count)
  const max = Math.max(...counts, 1)
  const total = counts.reduce((a, b) => a + b, 0)
  const points = days.map((d, idx) => {
    const x = Math.round((idx / 6) * 320)
    const y = Math.round(140 - (d.count / max) * 110)
    return { x, y }
  })
  const linePath = points.map((p, i) => (i === 0 ? `M ${p.x} ${p.y}` : `L ${p.x} ${p.y}`)).join(' ')
  const areaPath = linePath + ` L 320 150 L 0 150 Z`
  return { days, points, line: linePath, area: areaPath, total }
})

const typeRepartition = computed(() => {
  const total = agentDemandes.value.length || 1
  return TYPES_ACTES.map(t => {
    const count = agentDemandes.value.filter(d => d.type_acte === t.value).length
    return {
      value: t.value,
      label: t.label,
      count,
      share: Math.round((count / total) * 100)
    }
  })
})

onMounted(() => {
  window.addEventListener('hashchange', onHashChange)
  startCarousel()
})

onBeforeUnmount(() => {
  window.removeEventListener('hashchange', onHashChange)
  stopCarousel()
  clearTimeout(noticeTimer)
  if ('speechSynthesis' in window) window.speechSynthesis.cancel()
})
</script>

<template>
  <!-- =============================================================
       PORTAIL PUBLIC CITOYEN
       ============================================================= -->
  <div v-if="!isAgentRoute" class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <div class="h-1 w-full flex" aria-hidden="true">
      <span class="flex-1 bg-[#008751]"></span><span class="flex-1 bg-[#FCD116]"></span><span class="flex-1 bg-[#E8112D]"></span>
    </div>

    <!-- En-tête Institutionnel -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">
        <a href="#/" class="flex items-center gap-3 min-w-0">
          <span class="flex h-9 w-12 sm:h-10 sm:w-14 shrink-0 overflow-hidden rounded-md ring-1 ring-slate-200" aria-hidden="true">
            <span class="w-2/5 bg-[#008751]"></span>
            <span class="flex w-3/5 flex-col"><span class="flex-1 bg-[#FCD116]"></span><span class="flex-1 bg-[#E8112D]"></span></span>
          </span>
          <span class="min-w-0">
            <span class="block text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.18em] text-[#008751]">République du Bénin</span>
            <span class="block text-base sm:text-lg font-extrabold leading-tight text-slate-900 truncate">ASIN</span>
            <span class="hidden md:block text-xs text-slate-500 truncate">Agence des Systèmes d'Information et du Numérique</span>
          </span>
        </a>

        <button
          id="btn-guide-vocal"
          type="button"
          @click="speakGuide"
          :aria-pressed="speakingKey === 'guide'"
          class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 sm:px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-[#008751] hover:text-[#008751] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#008751]/40"
        >
          <Icon :name="speakingKey === 'guide' ? 'stop' : 'speaker'" class="h-5 w-5" />
          <span class="hidden sm:inline">{{ speakingKey === 'guide' ? 'Arrêter la lecture' : 'Assistance vocale' }}</span>
        </button>
      </div>
    </header>

    <!-- CARROUSEL HERO BÉNINOIS (3 IMAGES BÉNINOISES) -->
    <section class="relative overflow-hidden bg-slate-900 text-white min-h-[320px] sm:min-h-[420px] flex items-center">
      <div 
        v-for="(slide, index) in carouselSlides" 
        :key="index"
        :class="['absolute inset-0 transition-opacity duration-1000 ease-in-out', index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0']"
      >
        <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover object-center filter brightness-[0.45]" />
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center py-12 sm:py-16">
          <span class="inline-flex items-center gap-2 self-start rounded-full bg-white/10 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-[#FCD116] ring-1 ring-inset ring-white/20 backdrop-blur">
            <span class="h-1.5 w-1.5 rounded-full bg-[#FCD116]"></span>
            {{ slide.badge }}
          </span>
          <h1 class="mt-4 max-w-3xl text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-white drop-shadow-md">
            {{ slide.title }}
          </h1>
          <p class="mt-3 max-w-2xl text-sm sm:text-lg text-emerald-100/90 drop-shadow">
            {{ slide.subtitle }}
          </p>
        </div>
      </div>

      <!-- Commandes de navigation carrousel -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2 bg-slate-900/60 backdrop-blur px-3 py-1.5 rounded-full border border-white/15">
        <button @click="prevSlide" @mouseenter="stopCarousel" @mouseleave="startCarousel" aria-label="Précédent" class="p-1 rounded-full text-white/80 hover:text-white transition">
          <Icon name="chevronLeft" class="h-4 w-4" />
        </button>
        <div class="flex items-center gap-1.5">
          <button 
            v-for="(_, idx) in carouselSlides" 
            :key="idx" 
            @click="currentSlide = idx"
            :class="['h-2 rounded-full transition-all duration-300', idx === currentSlide ? 'w-6 bg-[#FCD116]' : 'w-2 bg-white/40']"
            :aria-label="`Diapositive ${idx + 1}`"
          ></button>
        </div>
        <button @click="nextSlide" @mouseenter="stopCarousel" @mouseleave="startCarousel" aria-label="Suivant" class="p-1 rounded-full text-white/80 hover:text-white transition">
          <Icon name="chevronRight" class="h-4 w-4" />
        </button>
      </div>
    </section>

    <!-- Contenu Principal Publique -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        <!-- Module 1: Recherche NPI & Tableau de Bord Citoyen -->
        <section aria-labelledby="titre-suivi" class="lg:col-span-3 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
          <div class="p-5 sm:p-8 border-b border-slate-100">
            <div class="flex items-start gap-4">
              <span class="hidden sm:flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#008751]">
                <Icon name="search" class="h-6 w-6" />
              </span>
              <div>
                <h2 id="titre-suivi" class="text-lg sm:text-xl font-bold text-slate-900">Tableau de bord Citoyen</h2>
                <p class="mt-1 text-sm text-slate-500">Saisissez votre Numéro Personnel d'Identification (10 chiffres) pour afficher le tableau de suivi de vos actes.</p>
              </div>
            </div>

            <form class="mt-6 grid grid-cols-1 sm:grid-cols-12 gap-3" @submit.prevent="chargerDemandesUsager">
              <label class="sm:col-span-5">
                <span class="sr-only">NPI</span>
                <input
                  id="input-recherche-npi"
                  v-model="rechercheNpi"
                  type="text" inputmode="numeric" maxlength="10" autocomplete="off"
                  placeholder="NPI — ex. 1234567890"
                  class="w-full rounded-xl border-0 bg-slate-50 px-4 py-3 font-mono text-base tracking-wider text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-[#008751]"
                />
              </label>
              <label class="sm:col-span-4">
                <span class="sr-only">Filtrer par statut</span>
                <select
                  id="select-filtre-statut"
                  v-model="filterStatutUsager"
                  class="w-full rounded-xl border-0 bg-slate-50 px-4 py-3 text-sm text-slate-700 ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-[#008751]"
                >
                  <option value="">Tous les statuts</option>
                  <option v-for="(m, key) in STATUTS" :key="key" :value="key">{{ m.label }}</option>
                </select>
              </label>
              <button
                id="btn-rechercher"
                type="submit"
                :disabled="loadingSearch"
                class="sm:col-span-3 inline-flex items-center justify-center gap-2 rounded-xl bg-[#008751] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#006b40] focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#008751] disabled:opacity-60"
              >
                <svg v-if="loadingSearch" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" /><path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
                <Icon v-else name="search" class="h-4 w-4" />
                Afficher
              </button>
            </form>

            <div v-if="searchErrorMessage" role="alert" class="mt-4 flex items-start gap-3 rounded-xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-inset ring-rose-200">
              <Icon name="warning" class="h-5 w-5 shrink-0 text-rose-500" />
              <span>{{ searchErrorMessage }}</span>
            </div>
          </div>

          <!-- TABLEAU DE BORD CITOYEN (RÉSULTATS DE L'USAGER CONNECTÉ PAR NPI) -->
          <div class="p-5 sm:p-8">
            <div v-if="demandesUsager.length" class="space-y-6">
              
              <!-- Cards d'Indicateurs Synthétiques Citoyen -->
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                  <div class="text-[11px] font-bold uppercase text-slate-400">Total Demandes</div>
                  <div class="text-xl font-bold text-slate-900 mt-0.5 tabular-nums">{{ citoyenStats.total }}</div>
                </div>
                <div class="bg-sky-50/60 p-3.5 rounded-xl border border-sky-200">
                  <div class="text-[11px] font-bold uppercase text-sky-700">Déposées</div>
                  <div class="text-xl font-bold text-sky-900 mt-0.5 tabular-nums">{{ citoyenStats.deposees }}</div>
                </div>
                <div class="bg-amber-50/60 p-3.5 rounded-xl border border-amber-200">
                  <div class="text-[11px] font-bold uppercase text-amber-700">En cours</div>
                  <div class="text-xl font-bold text-amber-900 mt-0.5 tabular-nums">{{ citoyenStats.enCours }}</div>
                </div>
                <div class="bg-emerald-50/60 p-3.5 rounded-xl border border-emerald-200">
                  <div class="text-[11px] font-bold uppercase text-emerald-700">Validées</div>
                  <div class="text-xl font-bold text-emerald-900 mt-0.5 tabular-nums">{{ citoyenStats.validees }}</div>
                </div>
              </div>

              <ul class="space-y-4">
                <li
                  v-for="d in demandesUsager" :key="d.reference"
                  class="group rounded-xl border border-slate-200 p-4 sm:p-5 transition hover:border-slate-300 hover:shadow-sm bg-white"
                >
                  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                      <span :class="['flex h-10 w-10 shrink-0 items-center justify-center rounded-lg', statutMeta(d.statut).accent]">
                        <Icon name="document" class="h-5 w-5" />
                      </span>
                      <div class="min-w-0">
                        <p class="font-bold text-slate-900 first-letter:uppercase text-base">{{ d.type_acte }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">
                          {{ d.nombre_copies }} exemplaire{{ d.nombre_copies > 1 ? 's' : '' }} · Déposée le {{ formatDate(d.created_at) }}
                        </p>
                        <p class="mt-1 font-mono text-[11px] text-slate-400 break-all">Réf UUID : {{ d.reference }}</p>
                      </div>
                    </div>
                    <span :class="['inline-flex items-center gap-1.5 self-start whitespace-nowrap rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset', statutMeta(d.statut).badge]">
                      <span :class="['h-1.5 w-1.5 rounded-full', statutMeta(d.statut).dot]"></span>
                      {{ statutMeta(d.statut).label }}
                    </span>
                  </div>

                  <!-- Indicateur visuel d'avancement (Timeline du Citoyen) -->
                  <div class="mt-4 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mb-1.5">
                      <span>Prise en charge</span>
                      <span>Instruction</span>
                      <span>Clôture finale</span>
                    </div>
                    <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden flex">
                      <div :class="['h-full transition-all duration-500', d.statut === 'déposée' ? 'w-1/3 bg-sky-500' : d.statut === 'en cours de traitement' ? 'w-2/3 bg-amber-500' : d.statut === 'validée' ? 'w-full bg-emerald-500' : 'w-full bg-rose-500']"></div>
                    </div>
                  </div>

                  <div v-if="d.statut === 'rejetée' && d.motif_rejet" class="mt-4 rounded-lg bg-rose-50 px-4 py-3 text-xs text-rose-800 ring-1 ring-inset ring-rose-100">
                    <span class="font-bold">Motif officiel du rejet :</span> {{ d.motif_rejet }}
                  </div>

                  <div class="mt-4 flex flex-wrap items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button
                      type="button"
                      @click="copyReference(d.reference)"
                      class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50 hover:text-[#008751]"
                    >
                      <Icon :name="copiedRef === d.reference ? 'check' : 'clipboard'" class="h-4 w-4" />
                      {{ copiedRef === d.reference ? 'Copié !' : 'Copier Réf' }}
                    </button>

                    <button
                      type="button"
                      @click="openRecepisse(d)"
                      class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-[#008751] bg-emerald-50 ring-1 ring-inset ring-emerald-200 transition hover:bg-emerald-100"
                    >
                      <Icon name="printer" class="h-4 w-4" />
                      Fiche Récépissé
                    </button>

                    <button
                      type="button"
                      @click="speakDemande(d)"
                      class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50 hover:text-[#008751]"
                    >
                      <Icon :name="speakingKey === d.reference ? 'stop' : 'speaker'" class="h-4 w-4" />
                      {{ speakingKey === d.reference ? 'Arrêter' : 'Écouter' }}
                    </button>
                  </div>
                </li>
              </ul>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-10 text-center">
              <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <Icon :name="rechercheFaite ? 'inbox' : 'search'" class="h-6 w-6" />
              </span>
              <p class="mt-4 text-sm font-medium text-slate-700">
                {{ rechercheFaite ? 'Aucune demande trouvée pour ce NPI.' : 'Votre tableau de bord citoyen s\'affichera ici.' }}
              </p>
              <p class="mt-1 text-xs text-slate-500">
                {{ rechercheFaite ? 'Vérifiez le NPI saisi ou déposez une nouvelle demande ci-contre.' : 'Saisissez votre NPI de 10 chiffres pour démarrer.' }}
              </p>
            </div>
          </div>
        </section>

        <!-- Module 2: Formulaire de Dépôt de Demande -->
        <section aria-labelledby="titre-depot" class="lg:col-span-2 lg:sticky lg:top-24 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
          <div class="p-5 sm:p-8">
            <div class="flex items-start gap-4">
              <span class="hidden sm:flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#008751]">
                <Icon name="document" class="h-6 w-6" />
              </span>
              <div>
                <h2 id="titre-depot" class="text-lg sm:text-xl font-bold text-slate-900">Nouvelle demande</h2>
                <p class="mt-1 text-sm text-slate-500">La demande est enregistrée au statut « Déposée ».</p>
              </div>
            </div>

            <form class="mt-6 space-y-5" @submit.prevent="deposerDemande" novalidate>
              <div>
                <label for="input-depot-npi" class="block text-sm font-medium text-slate-700">NPI (10 chiffres) *</label>
                <input
                  id="input-depot-npi"
                  v-model="nouveauDepot.npi"
                  type="text" inputmode="numeric" maxlength="10" autocomplete="off"
                  placeholder="10 chiffres"
                  :aria-invalid="!!formErrors.npi"
                  :class="['mt-1.5 w-full rounded-xl border-0 bg-slate-50 px-4 py-3 font-mono tracking-wider text-slate-900 ring-1 ring-inset placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-[#008751]', formErrors.npi ? 'ring-rose-300' : 'ring-slate-200']"
                />
                <p v-if="formErrors.npi" class="mt-1.5 text-xs text-rose-600">{{ formErrors.npi[0] }}</p>
              </div>

              <fieldset>
                <legend class="block text-sm font-medium text-slate-700">Type d'acte *</legend>
                <div class="mt-1.5 grid grid-cols-1 gap-2">
                  <label
                    v-for="t in TYPES_ACTES" :key="t.value"
                    :class="['flex cursor-pointer items-center gap-3 rounded-xl px-4 py-3 text-sm ring-1 ring-inset transition', nouveauDepot.type_acte === t.value ? 'bg-emerald-50/60 ring-[#008751] text-slate-900 font-semibold' : 'ring-slate-200 text-slate-700 hover:bg-slate-50']"
                  >
                    <input v-model="nouveauDepot.type_acte" type="radio" name="type_acte" :value="t.value" class="h-4 w-4 accent-[#008751]" />
                    {{ t.label }}
                  </label>
                </div>
                <p v-if="formErrors.type_acte" class="mt-1.5 text-xs text-rose-600">{{ formErrors.type_acte[0] }}</p>
              </fieldset>

              <div>
                <label for="input-depot-copies" class="block text-sm font-medium text-slate-700">Nombre d'exemplaires</label>
                <div class="mt-1.5 flex items-center gap-3">
                  <input
                    id="input-depot-copies"
                    v-model.number="nouveauDepot.nombre_copies"
                    type="range" min="1" max="5" step="1"
                    class="flex-1 accent-[#008751]"
                  />
                  <span class="flex h-10 w-12 items-center justify-center rounded-lg bg-slate-50 font-semibold text-slate-900 ring-1 ring-inset ring-slate-200">{{ nouveauDepot.nombre_copies }}</span>
                </div>
                <p v-if="formErrors.nombre_copies" class="mt-1.5 text-xs text-rose-600">{{ formErrors.nombre_copies[0] }}</p>
              </div>

              <div v-if="formSuccess" role="status" class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-900 ring-1 ring-inset ring-emerald-200">
                <div class="flex items-start gap-3">
                  <Icon name="checkCircle" class="h-5 w-5 shrink-0 text-emerald-600" />
                  <div class="min-w-0">
                    <p class="font-semibold">Demande enregistrée</p>
                    <p class="mt-1 text-xs text-emerald-800">Conservez votre référence :</p>
                    <p class="mt-1 font-mono text-xs break-all select-all">{{ formSuccess.reference }}</p>
                  </div>
                </div>
              </div>

              <div v-if="formErrorMessage" role="alert" class="flex items-start gap-3 rounded-xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-inset ring-rose-200">
                <Icon name="warning" class="h-5 w-5 shrink-0 text-rose-500" />
                <span>{{ formErrorMessage }}</span>
              </div>

              <button
                id="btn-soumettre-demande"
                type="submit"
                :disabled="submittingForm"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-slate-900 disabled:opacity-60"
              >
                {{ submittingForm ? 'Envoi en cours…' : 'Soumettre la demande' }}
                <Icon v-if="!submittingForm" name="arrowRight" class="h-4 w-4" />
              </button>
            </form>
          </div>
        </section>
      </div>
    </main>

    <!-- Pied de page -->
    <footer class="border-t border-slate-200 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-xs text-slate-500">
        <p><span class="font-semibold text-slate-700">ASIN</span> — Agence des Systèmes d'Information et du Numérique, Cotonou, Bénin</p>
        <p>© {{ new Date().getFullYear() }} République du Bénin. Tous droits réservés.</p>
      </div>
    </footer>
  </div>

  <!-- =============================================================
       ESPACE AGENT (URL dédiée : /#/agent)
       ============================================================= -->
  <div v-else class="text-slate-700">

    <!-- ---------------- Connexion (fond blanc) ---------------- -->
    <div v-if="!agentAuthenticated" class="min-h-screen bg-white flex">
      <div class="flex w-full lg:w-1/2 flex-col px-6 sm:px-12 xl:px-20">
        <div class="flex h-20 items-center">
          <a href="#/" class="flex items-center gap-3">
            <span class="flex h-8 w-11 shrink-0 overflow-hidden rounded-md ring-1 ring-slate-200" aria-hidden="true">
              <span class="w-2/5 bg-[#008751]"></span>
              <span class="flex w-3/5 flex-col"><span class="flex-1 bg-[#FCD116]"></span><span class="flex-1 bg-[#E8112D]"></span></span>
            </span>
            <span class="text-sm font-bold text-slate-900">ASIN <span class="font-normal text-slate-500">· Espace agent</span></span>
          </a>
        </div>

        <div class="flex flex-1 items-center">
          <div class="w-full max-w-sm mx-auto lg:mx-0">
            <h1 class="text-3xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-[#006b40] to-[#22b573]">Bon retour</h1>
            <p class="mt-2 text-sm text-slate-500">Saisissez votre code d'habilitation pour accéder à la console d'instruction.</p>

            <form class="mt-8 space-y-5" @submit.prevent="authenticateAgent">
              <div>
                <label for="input-code-agent" class="block text-sm font-semibold text-slate-700">Code d'habilitation</label>
                <div class="relative mt-2">
                  <Icon name="lock" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                  <input
                    id="input-code-agent"
                    v-model="agentCodeInput"
                    type="password" autocomplete="current-password"
                    placeholder="Votre code"
                    class="w-full rounded-xl border-0 bg-white py-3 pl-11 pr-4 text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-[#008751]"
                  />
                </div>
              </div>
              <p v-if="agentAuthError" role="alert" class="flex items-center gap-2 text-sm text-rose-600">
                <Icon name="warning" class="h-4 w-4" /> {{ agentAuthError }}
              </p>
              <button
                id="btn-connexion-agent"
                type="submit"
                class="w-full rounded-xl bg-gradient-to-tl from-[#006b40] to-[#22b573] px-4 py-3 text-sm font-bold uppercase tracking-wide text-white shadow-md shadow-emerald-600/20 transition hover:shadow-lg hover:shadow-emerald-600/30 hover:-translate-y-px focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#008751]"
              >
                Se connecter
              </button>
            </form>

            <a href="#/" class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#008751]">
              <Icon name="arrowLeft" class="h-4 w-4" /> Retour au portail public
            </a>
          </div>
        </div>

        <p class="py-6 text-xs text-slate-400">© {{ new Date().getFullYear() }} ASIN — République du Bénin</p>
      </div>

      <div class="hidden lg:block lg:w-1/2 p-4">
        <div class="relative h-full overflow-hidden rounded-3xl bg-gradient-to-br from-[#063d2a] via-[#0a5c3c] to-[#0f7a52]">
          <div class="absolute inset-0 opacity-[0.08] bg-[linear-gradient(to_right,#fff_1px,transparent_1px),linear-gradient(to_bottom,#fff_1px,transparent_1px)] bg-[size:40px_40px]" aria-hidden="true"></div>
          <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#22b573]/30 blur-3xl" aria-hidden="true"></div>
          <div class="relative flex h-full flex-col justify-end p-12 text-white">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
              <Icon name="shield" class="h-6 w-6 text-[#FCD116]" />
            </span>
            <h2 class="mt-6 text-3xl font-bold leading-tight">Console d'instruction<br />des actes administratifs</h2>
            <p class="mt-3 max-w-md text-sm text-emerald-100/80">Traitez les demandes des citoyens dans le respect du cycle de vie : prise en charge, validation ou rejet motivé.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ---------------- Tableau de bord (Dashboard PRO Soft UI Style) ---------------- -->
    <div v-else class="min-h-screen bg-[#f6f8fa]">
      <!-- Voile mobile -->
      <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

      <!-- Barre latérale -->
      <aside :class="['fixed inset-y-0 left-0 z-50 w-72 p-4 transition-transform duration-300 lg:translate-x-0', sidebarOpen ? 'translate-x-0' : '-translate-x-full']">
        <div class="flex h-full flex-col rounded-2xl bg-white p-4 shadow-[0_20px_27px_0_rgba(0,0,0,0.05)]">
          <div class="flex items-center justify-between px-2 py-3">
            <a href="#/agent" class="flex items-center gap-3">
              <span class="flex h-8 w-11 shrink-0 overflow-hidden rounded-md ring-1 ring-slate-200" aria-hidden="true">
                <span class="w-2/5 bg-[#008751]"></span>
                <span class="flex w-3/5 flex-col"><span class="flex-1 bg-[#FCD116]"></span><span class="flex-1 bg-[#E8112D]"></span></span>
              </span>
              <span class="text-sm font-bold text-slate-900">ASIN Instruction</span>
            </a>
            <button type="button" class="lg:hidden text-slate-400 hover:text-slate-700" @click="sidebarOpen = false" aria-label="Fermer le menu">
              <Icon name="close" class="h-5 w-5" />
            </button>
          </div>
          <div class="my-3 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

          <nav class="flex-1 space-y-1">
            <button
              v-for="item in AGENT_NAV" :key="item.key"
              type="button"
              @click="goTo(item.key)"
              :class="['flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition', agentView === item.key ? 'bg-white font-semibold text-slate-900 shadow-[0_8px_26px_-4px_rgba(20,20,20,0.15)]' : 'text-slate-500 hover:text-slate-900']"
            >
              <span :class="['flex h-8 w-8 items-center justify-center rounded-lg', agentView === item.key ? 'bg-gradient-to-tl from-[#006b40] to-[#22b573] text-white shadow' : 'bg-white text-slate-600 shadow-[0_4px_10px_-2px_rgba(20,20,20,0.12)]']">
                <Icon :name="item.icon" class="h-4 w-4" />
              </span>
              {{ item.label }}
            </button>

            <p class="px-3 pt-6 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Raccourcis</p>
            <a href="#/" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-500 transition hover:text-slate-900">
              <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-600 shadow-[0_4px_10px_-2px_rgba(20,20,20,0.12)]"><Icon name="globe" class="h-4 w-4" /></span>
              Portail public
            </a>
          </nav>

          <div class="relative mt-4 overflow-hidden rounded-2xl bg-gradient-to-tl from-[#063d2a] to-[#0f7a52] p-4 text-white">
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10" aria-hidden="true"></div>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><Icon name="document" class="h-4 w-4" /></span>
            <p class="mt-3 text-sm font-semibold">Documentation API</p>
            <p class="text-xs text-emerald-100/80">Référence OpenAPI des endpoints</p>
            <a :href="DOCS_URL" target="_blank" rel="noopener" class="mt-3 block rounded-lg bg-white py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-800 transition hover:bg-emerald-50">Consulter</a>
          </div>
        </div>
      </aside>

      <!-- Zone principale -->
      <div class="lg:pl-72">
        <header class="sticky top-0 z-30 px-4 sm:px-6 pt-4">
          <div class="flex flex-col gap-3 rounded-2xl bg-white/80 px-4 py-3 backdrop-blur-md shadow-[0_4px_20px_-6px_rgba(0,0,0,0.08)] sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <button type="button" class="lg:hidden flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100" @click="sidebarOpen = true" aria-label="Ouvrir le menu">
                <Icon name="menu" class="h-5 w-5" />
              </button>
              <div class="min-w-0">
                <p class="text-xs text-slate-400">Espace agent <span class="mx-1">/</span> <span class="text-slate-700">{{ currentNavLabel }}</span></p>
                <h1 class="text-base font-bold text-slate-900 truncate">{{ currentNavLabel }}</h1>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <label class="relative flex-1 sm:flex-none">
                <span class="sr-only">Rechercher</span>
                <Icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input
                  id="input-recherche-agent"
                  v-model="agentSearch"
                  @input="agentView = 'demandes'"
                  type="search" placeholder="NPI ou référence"
                  class="w-full sm:w-56 rounded-lg border-0 bg-white py-2 pl-9 pr-3 text-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-[#008751]"
                />
              </label>
              <span class="hidden md:flex items-center gap-2 px-2 text-sm font-medium text-slate-600">
                <Icon name="user" class="h-4 w-4" /> Agent
              </span>
              <button
                id="btn-deconnexion-agent"
                type="button" @click="logoutAgent"
                class="flex h-9 items-center gap-2 rounded-lg px-3 text-sm font-medium text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900"
              >
                <Icon name="logout" class="h-4 w-4" />
                <span class="hidden sm:inline">Déconnexion</span>
              </button>
            </div>
          </div>
        </header>

        <main class="px-4 sm:px-6 py-6 space-y-6">
          <!-- Notification -->
          <transition enter-from-class="opacity-0 -translate-y-2" enter-active-class="transition duration-200" leave-to-class="opacity-0" leave-active-class="transition duration-200">
            <div
              v-if="agentNotice" role="status"
              :class="['flex items-start gap-3 rounded-xl bg-white p-4 text-sm shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] ring-1 ring-inset', agentNotice.type === 'success' ? 'text-emerald-800 ring-emerald-200' : 'text-rose-800 ring-rose-200']"
            >
              <Icon :name="agentNotice.type === 'success' ? 'checkCircle' : 'warning'" :class="['h-5 w-5 shrink-0', agentNotice.type === 'success' ? 'text-emerald-500' : 'text-rose-500']" />
              <span class="flex-1">{{ agentNotice.text }}</span>
              <button type="button" class="text-slate-400 hover:text-slate-700" @click="agentNotice = null" aria-label="Fermer">
                <Icon name="close" class="h-4 w-4" />
              </button>
            </div>
          </transition>

          <!-- ========== Vue : Tableau de bord ========== -->
          <template v-if="agentView === 'dashboard'">
            <!-- Indicateurs -->
            <section aria-label="Indicateurs" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
              <div v-for="c in statCards" :key="c.key" class="flex items-center justify-between rounded-2xl bg-white p-4 sm:p-5 shadow-[0_20px_27px_0_rgba(0,0,0,0.05)]">
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-slate-500 truncate">{{ c.label }}</p>
                  <p class="mt-1 text-xl font-bold text-slate-900 tabular-nums">
                    {{ c.value }}
                    <span class="ml-1 text-sm font-bold text-emerald-500">{{ c.share }}%</span>
                  </p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tl from-[#006b40] to-[#22b573] text-white shadow-md shadow-emerald-600/20">
                  <Icon :name="c.icon" class="h-6 w-6" />
                </span>
              </div>
            </section>

            <!-- Graphiques -->
            <section class="grid grid-cols-1 lg:grid-cols-5 gap-6">
              <!-- Barres : répartition par statut -->
              <div class="lg:col-span-2 rounded-2xl bg-white p-4 shadow-[0_20px_27px_0_rgba(0,0,0,0.05)]">
                <div class="rounded-xl bg-gradient-to-tl from-slate-900 to-slate-700 px-4 pt-6 pb-3">
                  <div class="flex h-44 items-end justify-around gap-4">
                    <div v-for="b in statusBars" :key="b.key" class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                      <span class="text-xs font-semibold text-white tabular-nums">{{ b.value }}</span>
                      <div class="w-2.5 rounded-full bg-white transition-all duration-500" :style="{ height: b.height + '%' }"></div>
                    </div>
                  </div>
                  <div class="mt-3 flex justify-around gap-4 border-t border-white/10 pt-2">
                    <span v-for="b in statusBars" :key="b.key" class="flex-1 text-center text-[10px] font-medium text-slate-300 truncate">{{ b.short }}</span>
                  </div>
                </div>
                <div class="px-2 pt-5 pb-1">
                  <h2 class="text-base font-bold text-slate-900">Répartition par statut</h2>
                  <p class="text-sm text-slate-500"><span class="font-bold text-slate-700">{{ totalDemandes }}</span> demandes au total</p>
                </div>
              </div>

              <!-- Aire : activité des 7 derniers jours -->
              <div class="lg:col-span-3 rounded-2xl bg-white p-5 shadow-[0_20px_27px_0_rgba(0,0,0,0.05)]">
                <h2 class="text-base font-bold text-slate-900">Activité des 7 derniers jours</h2>
                <p class="text-sm text-slate-500">
                  <span class="font-bold text-emerald-500">{{ weekActivity.total }}</span> dépôt{{ weekActivity.total > 1 ? 's' : '' }} parmi les {{ agentDemandes.length }} dernières demandes
                </p>
                <svg viewBox="0 0 320 150" class="mt-4 w-full h-52" preserveAspectRatio="none" role="img" aria-label="Courbe des dépôts sur 7 jours">
                  <defs>
                    <linearGradient id="aire-verte" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#22b573" stop-opacity="0.35" />
                      <stop offset="100%" stop-color="#22b573" stop-opacity="0" />
                    </linearGradient>
                  </defs>
                  <g stroke="#e2e8f0" stroke-dasharray="4 4" stroke-width="0.6">
                    <line v-for="i in 4" :key="i" x1="0" x2="320" :y1="i * 30" :y2="i * 30" />
                  </g>
                  <path :d="weekActivity.area" fill="url(#aire-verte)" />
                  <path :d="weekActivity.line" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" />
                  <circle v-for="p in weekActivity.points" :key="p.x" :cx="p.x" :cy="p.y" r="3" fill="#fff" stroke="#16a34a" stroke-width="2" />
                </svg>
                <div class="mt-1 flex justify-between px-1 text-[11px] font-medium text-slate-400">
                  <span v-for="d in weekActivity.days" :key="d.key" class="capitalize">{{ d.label }}</span>
                </div>
              </div>
            </section>

            <!-- Demandes récentes + répartition par acte -->
            <section class="grid grid-cols-1 lg:grid-cols-5 gap-6">
              <div class="lg:col-span-3 rounded-2xl bg-white shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] overflow-hidden">
                <div class="flex items-center justify-between p-5">
                  <div>
                    <h2 class="text-base font-bold text-slate-900">Demandes récentes</h2>
                    <p class="text-sm text-slate-500">Les 5 derniers dépôts</p>
                  </div>
                  <button type="button" @click="goTo('demandes')" class="inline-flex items-center gap-1 text-sm font-semibold text-[#008751] hover:underline">
                    Tout voir <Icon name="arrowRight" class="h-4 w-4" />
                  </button>
                </div>
                <ul class="divide-y divide-slate-100">
                  <li v-for="d in recentDemandes" :key="d.reference" class="flex items-center justify-between gap-4 px-5 py-3">
                    <div class="flex items-center gap-3 min-w-0">
                      <span :class="['flex h-9 w-9 shrink-0 items-center justify-center rounded-lg', statutMeta(d.statut).accent]">
                        <Icon name="document" class="h-4 w-4" />
                      </span>
                      <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate first-letter:uppercase">{{ d.type_acte }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ d.npi }}</p>
                      </div>
                    </div>
                    <div class="text-right shrink-0">
                      <span :class="['inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset', statutMeta(d.statut).badge]">
                        <span :class="['h-1.5 w-1.5 rounded-full', statutMeta(d.statut).dot]"></span>{{ statutMeta(d.statut).label }}
                      </span>
                      <p class="mt-1 text-[11px] text-slate-400">{{ formatDate(d.created_at) }}</p>
                    </div>
                  </li>
                  <li v-if="!recentDemandes.length" class="px-5 py-10 text-center text-sm text-slate-400">Aucune demande enregistrée.</li>
                </ul>
              </div>

              <div class="lg:col-span-2 rounded-2xl bg-white p-5 shadow-[0_20px_27px_0_rgba(0,0,0,0.05)]">
                <h2 class="text-base font-bold text-slate-900">Types d'actes demandés</h2>
                <p class="text-sm text-slate-500">Sur les {{ agentDemandes.length }} dernières demandes</p>
                <ul class="mt-6 space-y-5">
                  <li v-for="t in typeRepartition" :key="t.value">
                    <div class="flex items-center justify-between text-sm">
                      <span class="font-medium text-slate-700">{{ t.label }}</span>
                      <span class="font-bold text-slate-900 tabular-nums">{{ t.count }} <span class="font-normal text-slate-400">· {{ t.share }}%</span></span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                      <div class="h-full rounded-full bg-gradient-to-r from-[#006b40] to-[#22b573] transition-all duration-500" :style="{ width: t.share + '%' }"></div>
                    </div>
                  </li>
                </ul>
              </div>
            </section>
          </template>

          <!-- ========== Vue : Demandes ========== -->
          <section v-else class="rounded-2xl bg-white shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5">
              <div>
                <h2 class="text-base font-bold text-slate-900">Demandes à instruire</h2>
                <p class="text-sm text-slate-500">{{ filteredAgentDemandes.length }} résultat{{ filteredAgentDemandes.length > 1 ? 's' : '' }}</p>
              </div>
              <div class="flex items-center gap-2">
                <select
                  id="select-filtre-agent"
                  v-model="agentFilterStatut"
                  @change="loadAgentDemandes"
                  class="flex-1 sm:flex-none rounded-lg border-0 bg-white px-3 py-2 text-sm text-slate-700 ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-[#008751]"
                >
                  <option value="">Tous les statuts</option>
                  <option v-for="(m, key) in STATUTS" :key="key" :value="key">{{ m.label }}</option>
                </select>
                <button
                  type="button" @click="refreshAgent" aria-label="Actualiser"
                  class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50"
                >
                  <Icon name="refresh" :class="['h-4 w-4', loadingAgent && 'animate-spin']" />
                </button>
              </div>
            </div>

            <!-- Tableau (desktop) -->
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead class="border-y border-slate-100 bg-slate-50/60 text-[11px] uppercase tracking-wider text-slate-400">
                  <tr>
                    <th class="px-5 py-3 font-bold">Demandeur</th>
                    <th class="px-5 py-3 font-bold">Acte</th>
                    <th class="px-5 py-3 font-bold text-center">Ex.</th>
                    <th class="px-5 py-3 font-bold">Statut</th>
                    <th class="px-5 py-3 font-bold">Dépôt</th>
                    <th class="px-5 py-3 font-bold text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="d in filteredAgentDemandes" :key="d.reference" class="transition hover:bg-slate-50/60">
                    <td class="px-5 py-4">
                      <p class="font-mono font-semibold text-slate-900">{{ d.npi }}</p>
                      <p class="font-mono text-[11px] text-slate-400" :title="d.reference">{{ d.reference.slice(0, 8) }}…</p>
                    </td>
                    <td class="px-5 py-4 text-slate-600 first-letter:uppercase">{{ d.type_acte }}</td>
                    <td class="px-5 py-4 text-center text-slate-600 tabular-nums">{{ d.nombre_copies }}</td>
                    <td class="px-5 py-4">
                      <span :class="['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset', statutMeta(d.statut).badge]">
                        <span :class="['h-1.5 w-1.5 rounded-full', statutMeta(d.statut).dot]"></span>{{ statutMeta(d.statut).label }}
                      </span>
                      <p v-if="d.motif_rejet" class="mt-1 max-w-[16rem] truncate text-xs text-rose-600" :title="d.motif_rejet">{{ d.motif_rejet }}</p>
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">{{ formatDate(d.created_at) }}</td>
                    <td class="px-5 py-4">
                      <div class="flex justify-end gap-2">
                        <button v-if="d.statut === 'déposée'" type="button" @click="updateStatutAgent(d, 'en cours de traitement')"
                          class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                          Prendre en charge
                        </button>
                        <template v-else-if="d.statut === 'en cours de traitement'">
                          <button type="button" @click="updateStatutAgent(d, 'validée')"
                            class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-tl from-[#006b40] to-[#22b573] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:shadow">
                            <Icon name="check" class="h-3.5 w-3.5" /> Valider
                          </button>
                          <button type="button" @click="openRejetModal(d)"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-600 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-50">
                            <Icon name="close" class="h-3.5 w-3.5" /> Rejeter
                          </button>
                        </template>
                        <span v-else class="inline-flex items-center gap-1 text-xs text-slate-400">
                          <Icon name="lock" class="h-3.5 w-3.5" /> Clôturée
                        </span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Cartes (mobile) -->
            <ul class="md:hidden divide-y divide-slate-100 border-t border-slate-100">
              <li v-for="d in filteredAgentDemandes" :key="d.reference" class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <p class="font-mono font-semibold text-slate-900">{{ d.npi }}</p>
                    <p class="text-sm text-slate-600 first-letter:uppercase">{{ d.type_acte }} · {{ d.nombre_copies }} ex.</p>
                    <p class="text-xs text-slate-400">{{ formatDate(d.created_at) }}</p>
                  </div>
                  <span :class="['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset', statutMeta(d.statut).badge]">
                    <span :class="['h-1.5 w-1.5 rounded-full', statutMeta(d.statut).dot]"></span>{{ statutMeta(d.statut).label }}
                  </span>
                </div>
                <p v-if="d.motif_rejet" class="text-xs text-rose-600">{{ d.motif_rejet }}</p>
                <div class="flex gap-2">
                  <button v-if="d.statut === 'déposée'" type="button" @click="updateStatutAgent(d, 'en cours de traitement')"
                    class="flex-1 rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">
                    Prendre en charge
                  </button>
                  <template v-else-if="d.statut === 'en cours de traitement'">
                    <button type="button" @click="updateStatutAgent(d, 'validée')"
                      class="flex-1 rounded-lg bg-gradient-to-tl from-[#006b40] to-[#22b573] px-3 py-2 text-xs font-semibold text-white">
                      Valider
                    </button>
                    <button type="button" @click="openRejetModal(d)"
                      class="flex-1 rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 ring-1 ring-inset ring-rose-200">
                      Rejeter
                    </button>
                  </template>
                </div>
              </li>
            </ul>

            <div v-if="!filteredAgentDemandes.length && !loadingAgent" class="px-6 py-14 text-center border-t border-slate-100">
              <Icon name="inbox" class="mx-auto h-8 w-8 text-slate-300" />
              <p class="mt-3 text-sm text-slate-500">Aucune demande à afficher.</p>
            </div>
          </section>
        </main>
      </div>
    </div>

    <!-- Modale de rejet -->
    <div v-if="showRejetModal" class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-slate-900/40 backdrop-blur-sm p-0 sm:p-4" role="dialog" aria-modal="true" aria-labelledby="titre-rejet">
      <div class="w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
              <Icon name="xCircle" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
              <h3 id="titre-rejet" class="text-base font-bold text-slate-900">Rejeter la demande</h3>
              <p class="mt-0.5 text-xs text-slate-400 font-mono break-all">{{ selectedDemandeRejet?.reference }}</p>
            </div>
          </div>
          <button type="button" @click="showRejetModal = false" class="text-slate-400 hover:text-slate-700" aria-label="Fermer">
            <Icon name="close" class="h-5 w-5" />
          </button>
        </div>
        <label for="textarea-motif-rejet" class="mt-5 block text-sm font-semibold text-slate-700">Motif du rejet</label>
        <textarea
          id="textarea-motif-rejet"
          v-model="motifRejetInput"
          rows="4" maxlength="500"
          placeholder="Précisez la raison du rejet (5 caractères minimum)"
          class="mt-2 w-full rounded-xl border-0 p-3 text-sm text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-rose-500"
        ></textarea>
        <div class="mt-1 flex justify-between text-xs">
          <span class="text-rose-600">{{ rejetErrorMessage }}</span>
          <span class="text-slate-400 tabular-nums">{{ motifRejetInput.length }}/500</span>
        </div>
        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
          <button type="button" @click="showRejetModal = false" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50">
            Annuler
          </button>
          <button id="btn-confirmer-rejet" type="button" @click="confirmRejet" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-500">
            Confirmer le rejet
          </button>
        </div>
      </div>
    </div>

    <!-- Modale & Document Récépissé Officiel (Impression A4) -->
    <div v-if="showRecepisseModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" role="dialog" aria-modal="true">
      <div class="w-full max-w-2xl rounded-2xl bg-white p-6 sm:p-8 shadow-2xl space-y-6">
        <!-- Header Non-Imprimable -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 print:hidden">
          <div class="flex items-center gap-2">
            <Icon name="document" class="h-6 w-6 text-[#008751]" />
            <h3 class="text-lg font-bold text-slate-900">Récépissé Officiel de Dépôt</h3>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" @click="triggerPrint" class="inline-flex items-center gap-2 rounded-xl bg-[#008751] px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-[#006b40]">
              <Icon name="printer" class="h-4 w-4" />
              Imprimer / PDF
            </button>
            <button type="button" @click="showRecepisseModal = false" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
              <Icon name="close" class="h-5 w-5" />
            </button>
          </div>
        </div>

        <!-- ZONE IMPRIMABLE DU RÉCÉPISSÉ -->
        <div id="section-recepisse-print" class="space-y-6 border border-slate-200 rounded-xl p-6 bg-slate-50/50">
          <div class="flex items-start justify-between border-b-2 border-[#008751] pb-4">
            <div>
              <p class="text-xs font-bold tracking-widest text-[#008751] uppercase">RÉPUBLIQUE DU BÉNIN</p>
              <p class="text-[10px] text-slate-500">Fraternité - Justice - Travail</p>
              <h2 class="mt-2 text-base font-extrabold text-slate-900">ASIN BÉNIN · ATTESTATION DE DÉPÔT</h2>
              <p class="text-xs text-slate-500">Système National de Suivi des Actes Administratifs</p>
            </div>
            <!-- Visuel QR Code officiel -->
            <div class="flex flex-col items-center bg-white p-2 rounded-lg border border-slate-200 shadow-xs">
              <svg class="h-16 w-16 text-slate-800" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 15h6v6H3v-6zm2 2v2h2v-2H5zm10 0h2v2h-2v-2zm2-2h2v2h-2v-2zm-2-2h2v2h-2v-2zm4 4h2v2h-2v-2zm-2 2h2v2h-2v-2z"/>
              </svg>
              <span class="mt-1 text-[9px] font-mono font-bold text-slate-400">VÉRIFICATION</span>
            </div>
          </div>

          <div v-if="selectedRecepisse" class="grid grid-cols-2 gap-4 text-xs">
            <div class="bg-white p-3 rounded-lg border border-slate-200">
              <span class="text-slate-400 font-medium block">Titulaire (NPI)</span>
              <span class="font-mono font-bold text-slate-900 text-sm tracking-wider">{{ selectedRecepisse.npi }}</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
              <span class="text-slate-400 font-medium block">Référence Unique (UUID)</span>
              <span class="font-mono font-bold text-[#008751] text-xs break-all">{{ selectedRecepisse.reference }}</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
              <span class="text-slate-400 font-medium block">Type d'Acte Demande</span>
              <span class="font-bold text-slate-900 first-letter:uppercase">{{ selectedRecepisse.type_acte }}</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
              <span class="text-slate-400 font-medium block">Nombre d'Exemplaires</span>
              <span class="font-bold text-slate-900">{{ selectedRecepisse.nombre_copies }} exemplaire{{ selectedRecepisse.nombre_copies > 1 ? 's' : '' }}</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
              <span class="text-slate-400 font-medium block">Date & Heure d'Enregistrement</span>
              <span class="font-medium text-slate-800">{{ formatDate(selectedRecepisse.created_at) }}</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
              <span class="text-slate-400 font-medium block">Statut Actuel</span>
              <span :class="['inline-flex items-center gap-1 font-bold mt-0.5', selectedRecepisse.statut === 'validée' ? 'text-emerald-700' : selectedRecepisse.statut === 'rejetée' ? 'text-rose-700' : 'text-amber-700']">
                {{ selectedRecepisse.statut }}
              </span>
            </div>
          </div>

          <div v-if="selectedRecepisse?.statut === 'rejetée' && selectedRecepisse?.motif_rejet" class="bg-rose-50 p-3 rounded-lg border border-rose-200 text-xs text-rose-800">
            <span class="font-bold">Motif du rejet :</span> {{ selectedRecepisse.motif_rejet }}
          </div>

          <div class="border-t border-slate-200 pt-4 text-[10px] text-slate-500 space-y-1">
            <p><span class="font-bold">Remarque importante :</span> Ce récépissé est une preuve officielle de dépôt d'acte administratif auprès de l'ASIN Bénin.</p>
            <p>Conservez précieusement la référence UUID pour suivre à tout moment l'état d'avancement de votre dossier sur le portail public.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
