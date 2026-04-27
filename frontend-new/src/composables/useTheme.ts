import { ref } from 'vue'

export type Theme = 'light' | 'dark' | 'system'

const STORAGE_KEY = 'clean-api-theme'

const theme = ref<Theme>((localStorage.getItem(STORAGE_KEY) as Theme) || 'system')

export function useTheme() {
  const resolvedTheme = ref<'light' | 'dark'>('light')

  function applyTheme(themeValue: 'light' | 'dark') {
    const root = document.documentElement
    if (themeValue === 'dark') {
      root.classList.add('dark')
    } else {
      root.classList.remove('dark')
    }
    resolvedTheme.value = themeValue
  }

  function getSystemTheme(): 'light' | 'dark' {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  function setTheme(newTheme: Theme) {
    theme.value = newTheme
    localStorage.setItem(STORAGE_KEY, newTheme)

    if (newTheme === 'system') {
      applyTheme(getSystemTheme())
    } else {
      applyTheme(newTheme)
    }
  }

  function toggleTheme() {
    if (theme.value === 'system') {
      setTheme(getSystemTheme() === 'dark' ? 'light' : 'dark')
    } else {
      setTheme(theme.value === 'dark' ? 'light' : 'dark')
    }
  }

  // Initialize
  setTheme(theme.value)

  // Listen for system theme changes
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', () => {
    if (theme.value === 'system') {
      applyTheme(getSystemTheme())
    }
  })

  return {
    theme,
    resolvedTheme,
    setTheme,
    toggleTheme,
  }
}
