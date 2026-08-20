import type { ButtonHTMLAttributes, InputHTMLAttributes, ReactNode, TextareaHTMLAttributes } from 'react'
import { Icon, type IconName } from './Icon'

/* ---------- Button ---------- */

type Variant = 'filled' | 'tonal' | 'outlined' | 'text'
type Size = 'sm' | 'md' | 'lg'

const variantCls: Record<Variant, string> = {
  filled: 'bg-primary text-on-primary hover:brightness-95 active:brightness-90 shadow-sm',
  tonal: 'bg-secondary-container text-on-secondary-container hover:brightness-[0.97]',
  outlined:
    'border border-outline text-primary hover:bg-primary/5 active:bg-primary/10',
  text: 'text-primary hover:bg-primary/5 active:bg-primary/10',
}

const sizeCls: Record<Size, string> = {
  sm: 'h-9 px-3 text-sm gap-1.5 rounded-full',
  md: 'h-11 px-5 text-sm gap-2 rounded-full',
  lg: 'h-13 px-7 text-base gap-2.5 rounded-full',
}

interface BtnProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant
  size?: Size
  icon?: IconName
  loading?: boolean
}

export function Button({
  variant = 'filled',
  size = 'md',
  icon,
  loading,
  children,
  className = '',
  disabled,
  ...rest
}: BtnProps) {
  return (
    <button
      disabled={disabled || loading}
      className={`inline-flex items-center justify-center font-medium tracking-wide select-none transition-colors disabled:opacity-40 disabled:pointer-events-none ${variantCls[variant]} ${sizeCls[size]} ${className}`}
      {...rest}
    >
      {loading ? (
        <Spinner className="size-4" />
      ) : (
        icon && <Icon name={icon} size={size === 'lg' ? 22 : 18} />
      )}
      {children}
    </button>
  )
}

/* ---------- Card ---------- */

export function Card({
  children,
  className = '',
  onClick,
}: {
  children: ReactNode
  className?: string
  onClick?: () => void
}) {
  return (
    <div
      onClick={onClick}
      className={`rounded-lg bg-surface-low px-4 py-4 ${onClick ? 'cursor-pointer active:scale-[0.99] transition-transform' : ''} ${className}`}
    >
      {children}
    </div>
  )
}

/* ---------- Field ---------- */

interface FieldProps extends InputHTMLAttributes<HTMLInputElement> {
  label: string
  hint?: string
  error?: string
}

export function Field({ label, hint, error, className = '', ...rest }: FieldProps) {
  const id = rest.id ?? `f-${label.replace(/\s+/g, '-').toLowerCase()}`
  return (
    <div className="flex flex-col gap-1.5">
      <label htmlFor={id} className="text-sm font-medium text-on-surface-variant">
        {label}
      </label>
      <input
        id={id}
        className={`h-12 rounded-md border bg-surface-lowest px-4 text-base outline-none transition-colors placeholder:text-on-surface-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 ${error ? 'border-error' : 'border-outline-variant'} ${className}`}
        {...rest}
      />
      {error ? (
        <p className="text-xs text-error">{error}</p>
      ) : hint ? (
        <p className="text-xs text-on-surface-variant">{hint}</p>
      ) : null}
    </div>
  )
}

export function TextFieldArea({
  label,
  error,
  className = '',
  ...rest
}: TextareaHTMLAttributes<HTMLTextAreaElement> & { label: string; error?: string }) {
  const id = rest.id ?? `f-${label.replace(/\s+/g, '-').toLowerCase()}`
  return (
    <div className="flex flex-col gap-1.5">
      <label htmlFor={id} className="text-sm font-medium text-on-surface-variant">
        {label}
      </label>
      <textarea
        id={id}
        className={`min-h-28 rounded-md border bg-surface-lowest px-4 py-3 text-base outline-none transition-colors placeholder:text-on-surface-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 ${error ? 'border-error' : 'border-outline-variant'} ${className}`}
        {...rest}
      />
      {error && <p className="text-xs text-error">{error}</p>}
    </div>
  )
}

/* ---------- Badge ---------- */

export function Badge({
  tone = 'primary',
  children,
}: {
  tone?: 'primary' | 'success' | 'error' | 'neutral'
  children: ReactNode
}) {
  const map = {
    primary: 'bg-primary-container text-on-primary-container',
    success: 'bg-secondary-container text-on-secondary-container',
    error: 'bg-error-container text-on-error-container',
    neutral: 'bg-surface-variant text-on-surface-variant',
  }
  return (
    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ${map[tone]}`}>
      {children}
    </span>
  )
}

/* ---------- Spinner, Empty, Skeleton ---------- */

export function Spinner({ className = 'size-5' }: { className?: string }) {
  return (
    <span
      className={`inline-block animate-spin rounded-full border-2 border-current border-t-transparent ${className}`}
    />
  )
}

export function Empty({ text }: { text: string }) {
  return (
    <div className="flex flex-col items-center gap-3 py-14 text-center">
      <Icon name="info" size={40} className="text-outline" />
      <p className="max-w-60 text-sm text-on-surface-variant">{text}</p>
    </div>
  )
}

/* ---------- Status tone helper ---------- */

export function statusTone(status: string | undefined): 'success' | 'error' | 'neutral' {
  if (!status) return 'neutral'
  const s = String(status).toLowerCase()
  if (s.includes('lunas')) return 'success'
  if (s.includes('belum')) return 'error'
  return 'neutral'
}

/* ---------- Page header ---------- */

export function PageHeader({
  title,
  sub,
  onBack,
  right,
}: {
  title: string
  sub?: string
  onBack?: () => void
  right?: ReactNode
}) {
  return (
    <header className="sticky top-0 z-20 flex items-center gap-2 bg-surface/90 px-4 py-3 backdrop-blur safe-top">
      {onBack && (
        <button onClick={onBack} className="-ml-1 flex size-10 items-center justify-center rounded-full hover:bg-surface-variant">
          <Icon name="chevronLeft" />
        </button>
      )}
      <div className="min-w-0 flex-1">
        <h1 className="truncate text-lg font-semibold text-on-surface">{title}</h1>
        {sub && <p className="truncate text-xs text-on-surface-variant">{sub}</p>}
      </div>
      {right}
    </header>
  )
}