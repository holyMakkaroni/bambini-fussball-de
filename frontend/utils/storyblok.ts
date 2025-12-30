export const removeTrailingSlash = (path: string): string => {
  if (path !== '/' && path.endsWith('/')) {
    return path.slice(0, -1)
  }
  return path
}

export const cleanUrl = (path: string, websiteHandle: string): string => {
  return `/${removeTrailingSlash(path.replace(`${websiteHandle}/`, ''))}`
}
