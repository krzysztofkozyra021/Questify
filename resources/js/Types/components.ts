export interface ButtonProps {
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
  loading?: boolean
}

export interface ModalProps {
  show: boolean
  maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl'
  closeable?: boolean
}

export interface DropdownProps {
  align?: 'left' | 'right'
  width?: '48'
  contentClasses?: string
}

export interface NotificationProps {
  showNotification: boolean
  textToDisplay: string
  type?: 'success' | 'error' | 'warning' | 'info'
}

export interface OptionButtonProps {
  optionText: string
  route: string
  method?: string
} 
