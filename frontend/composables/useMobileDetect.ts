import MobileDetect from 'mobile-detect'

export const useMobileDetect = () => {
  const userAgent = process.server
    ? useRequestHeaders()['user-agent']
    : navigator.userAgent

  const md = new MobileDetect(userAgent)

  const isMobile = computed(() => !!md.mobile())
  const isTablet = computed(() => !!md.tablet())
  const isPhone = computed(() => !!md.phone())
  const os = computed(() => md.os())
  const userAgentString = computed(() => md.userAgent())

  return {
    isMobile,
    isTablet,
    isPhone,
    os,
    userAgentString
  }
}
