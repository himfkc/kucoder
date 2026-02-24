import hasPermi from './permission/hasPermi'
import copyText from './common/copyText'
import kcAuth from './auth/kcAuth'

export default function directive(app){
  app.directive('hasPermi', hasPermi)
  app.directive('copyText', copyText)
  app.directive('auth', kcAuth)
}