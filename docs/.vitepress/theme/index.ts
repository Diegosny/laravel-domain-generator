import DefaultTheme from 'vitepress/theme'
import './custom.css'

import HeroDashboard from './components/HeroDashboard.vue'
import VersionBadge from './components/VersionBadge.vue'
import FeatureCard from './components/FeatureCard.vue'
import ApiMethod from './components/ApiMethod.vue'
import Callout from './components/Callout.vue'

export default {
  extends: DefaultTheme,

  enhanceApp({ app }) {
    app.component('HeroDashboard', HeroDashboard)
    app.component('VersionBadge', VersionBadge)
    app.component('FeatureCard', FeatureCard)
    app.component('ApiMethod', ApiMethod)
    app.component('Callout', Callout)
  }
}