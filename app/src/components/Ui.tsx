import type {
  ButtonHTMLAttributes,
  InputHTMLAttributes,
  ReactNode,
  TextareaHTMLAttributes,
} from 'react'
import { Icon, type IconName } from './Icon'

type ButtonVariant = 'filled' | 'tonal' | 'outlined' | 'text'
type ButtonSize = 'sm' | 'md' | 'lg'
type BadgeTone = 'primary' | 'success' | 'error' | 'neutral'
type CardVariant = 'filled' | 'outlined' | 'elevated' | 'tonal'

const buttonVariant: Record<ButtonVariant, string> = {
  filled:
    'bg-primary text-on-primary shadow-[0_6px_16px_color-mix(in_srgb,var(--ph-primary)_18%,transparent)] hover:brightness-95 active:brightness-90',
  tonal: 'bg-primary-container text-on-primary-container hover:brightness-[0.97]',
  outlined: 'border border-outline-variant bg-surface-lowest text-primary hover:bg-primary/5',
  text: 'text-primary hover:bg-primary/7',
}

const buttonSize: Record<ButtonSize, string> = {
  sm: 'min-h-10 px-3.5 text-[13px] gap-1.5',
  md: 'min-h-11 px-4 text-sm gap-2',
  lg: 'min-h-12 px-5 text-sm gap-2',
}

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant
  size?: ButtonSize
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
}: ButtonProps) {
  return (
    <button
      disabled={disabled || loading}
      aria-busy={loading || undefined}
      className={`inline-flex items-center justify-center rounded-[.9rem] font-semibold select-none transition duration-200 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/30 disabled:pointer-events-none disabled:opacity-40 ${buttonVariant[variant]} ${buttonSize[size]} ${className}`}
      {...rest}
    >
      {loading ? <Spinner className="size-4" label="Memproses" /> : icon && <Icon name={icon} size={size === 'lg' ? 22 : 18} />}
      {children}
    </button>
  )
}

export function IconButton({
  icon,
  label,
  className = '',
  ...rest
}: ButtonHTMLAttributes<HTMLButtonElement> & { icon: IconName; label: string }) {
  return (
    <button
      aria-label={label}
      title={label}
      className={`inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant transition duration-200 hover:bg-surface-variant active:scale-95 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/30 ${className}`}
      {...rest}
    >
      <Icon name={icon} size={20} />
    </button>
  )
}

const cardVariant: Record<CardVariant, string> = {
  filled: 'bg-surface-low',
  outlined: 'border border-outline-variant/70 bg-surface-lowest',
  elevated: 'bg-surface-lowest shadow-[0_7px_22px_rgba(15,45,42,.065)]',
  tonal: 'bg-primary-container/55 text-on-primary-container',
}

export function Card({
  children,
  className = '',
  onClick,
  variant = 'filled',
}: {
  children: ReactNode
  className?: string
  onClick?: () => void
  variant?: CardVariant
}) {
  return (
    <div
      onClick={onClick}
      className={`rounded-[1.1rem] px-4 py-4 ${cardVariant[variant]} ${onClick ? 'cursor-pointer transition duration-200 hover:bg-surface-low active:scale-[.995]' : ''} ${className}`}
    >
      {children}
    </div>
  )
}

interface FieldProps extends InputHTMLAttributes<HTMLInputElement> {
  label: string
  hint?: string
  error?: string
  leadingIcon?: IconName
}

export function Field({ label, hint, error, leadingIcon, className = '', ...rest }: FieldProps) {
  const id = rest.id ?? `f-${label.replace(/\s+/g, '-').toLowerCase()}`
  const descriptionId = error || hint ? `${id}-description` : undefined
  return (
    <div className="flex flex-col gap-2">
      <label htmlFor={id} className="px-0.5 text-[13px] font-semibold text-on-surface-variant">
        {label}
      </label>
      <div className="relative">
        {leadingIcon && (
          <span className="pointer-events-none absolute inset-y-0 left-4 flex items-center text-on-surface-variant">
            <Icon name={leadingIcon} size={20} />
          </span>
        )}
        <input
          id={id}
          aria-invalid={Boolean(error)}
          aria-describedby={descriptionId}
          className={`min-h-12 w-full rounded-[.9rem] border bg-surface-lowest px-4 text-sm text-on-surface outline-none transition duration-200 placeholder:text-on-surface-variant/55 focus:border-primary focus:ring-3 focus:ring-primary/15 ${leadingIcon ? 'pl-12' : ''} ${error ? 'border-error' : 'border-outline-variant'} ${className}`}
          {...rest}
        />
      </div>
      {(error || hint) && (
        <p id={descriptionId} className={`px-0.5 text-xs ${error ? 'text-error' : 'text-on-surface-variant'}`}>
          {error || hint}
        </p>
      )}
    </div>
  )
}

export function TextFieldArea({
  label,
  error,
  hint,
  className = '',
  ...rest
}: TextareaHTMLAttributes<HTMLTextAreaElement> & { label: string; error?: string; hint?: string }) {
  const id = rest.id ?? `f-${label.replace(/\s+/g, '-').toLowerCase()}`
  const descriptionId = error || hint ? `${id}-description` : undefined
  return (
    <div className="flex flex-col gap-2">
      <label htmlFor={id} className="px-0.5 text-[13px] font-semibold text-on-surface-variant">
        {label}
      </label>
      <textarea
        id={id}
        aria-invalid={Boolean(error)}
        aria-describedby={descriptionId}
        className={`min-h-30 rounded-[.9rem] border bg-surface-lowest px-4 py-3 text-sm leading-relaxed text-on-surface outline-none transition duration-200 placeholder:text-on-surface-variant/55 focus:border-primary focus:ring-3 focus:ring-primary/15 ${error ? 'border-error' : 'border-outline-variant'} ${className}`}
        {...rest}
      />
      {(error || hint) && (
        <p id={descriptionId} className={`px-0.5 text-xs ${error ? 'text-error' : 'text-on-surface-variant'}`}>
          {error || hint}
        </p>
      )}
    </div>
  )
}

export function Badge({ tone = 'primary', children }: { tone?: BadgeTone; children: ReactNode }) {
  const map: Record<BadgeTone, string> = {
    primary: 'bg-primary-container text-on-primary-container',
    success: 'bg-success-container text-on-success-container',
    error: 'bg-error-container text-on-error-container',
    neutral: 'bg-surface-variant text-on-surface-variant',
  }
  return (
    <span className={`inline-flex min-h-6 items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${map[tone]}`}>
      {children}
    </span>
  )
}

export function Spinner({ className = 'size-5', label = 'Memuat' }: { className?: string; label?: string }) {
  return (
    <span role="status" aria-label={label} className={`inline-block animate-spin rounded-full border-2 border-current border-t-transparent ${className}`} />
  )
}

export function LoadingState({ label = 'Memuat data…' }: { label?: string }) {
  return (
    <div role="status" className="flex flex-col gap-3 py-4" aria-label={label}>
      <div className="h-24 animate-pulse rounded-[1.1rem] bg-surface-high" />
      <div className="h-18 animate-pulse rounded-[1.1rem] bg-surface-low" />
      <span className="sr-only">{label}</span>
    </div>
  )
}

export function Empty({
  text,
  title = 'Belum ada data',
  icon = 'info',
  action,
}: {
  text: string
  title?: string
  icon?: IconName
  action?: ReactNode
}) {
  return (
    <div className="flex flex-col items-center gap-3 rounded-[1.1rem] border border-dashed border-outline-variant bg-surface-low/50 px-6 py-10 text-center">
      <span className="flex size-12 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
        <Icon name={icon} size={23} />
      </span>
      <div>
        <p className="font-semibold text-on-surface">{title}</p>
        <p className="mx-auto mt-1 max-w-64 text-sm leading-relaxed text-on-surface-variant">{text}</p>
      </div>
      {action}
    </div>
  )
}

export function AlertBanner({
  tone = 'error',
  children,
  action,
}: {
  tone?: 'error' | 'success' | 'info'
  children: ReactNode
  action?: ReactNode
}) {
  const style = {
    error: 'bg-error-container text-on-error-container',
    success: 'bg-success-container text-on-success-container',
    info: 'bg-primary-container text-on-primary-container',
  }[tone]
  const icon: IconName = tone === 'error' ? 'alert' : tone === 'success' ? 'check' : 'info'
  return (
    <div role={tone === 'error' ? 'alert' : 'status'} className={`flex items-start gap-3 rounded-[.9rem] px-4 py-3 text-sm ${style}`}>
      <Icon name={icon} size={19} className="mt-0.5 shrink-0" />
      <div className="min-w-0 flex-1 leading-relaxed">{children}</div>
      {action}
    </div>
  )
}

export function SectionHeader({ title, sub, right }: { title: string; sub?: string; right?: ReactNode }) {
  return (
    <div className="flex items-end justify-between gap-3 px-0.5 pt-1">
      <div>
        <h2 className="text-[15px] font-semibold text-on-surface">{title}</h2>
        {sub && <p className="mt-0.5 text-xs text-on-surface-variant">{sub}</p>}
      </div>
      {right}
    </div>
  )
}

export function statusTone(status: string | undefined): BadgeTone {
  if (!status) return 'neutral'
  const s = String(status).trim().toLowerCase()
  if (s.includes('jatuh tempo') || s.includes('belum') || s.includes('gagal') || s.includes('tolak') || s.includes('batal')) return 'error'
  if (s.includes('warning')) return 'primary'
  if (s === 'lunas' || s.includes('selesai') || s.includes('berhasil') || s.includes('resolved')) return 'success'
  if (s.includes('proses') || s.includes('diajukan') || s.includes('menunggu') || s.includes('baru')) return 'primary'
  return 'neutral'
}

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
    <header className="safe-top sticky top-0 z-20 border-b border-outline-variant/40 bg-surface/90 px-4 backdrop-blur-xl">
      <div className="mx-auto flex min-h-15 max-w-[608px] items-center gap-2">
        {onBack && <IconButton icon="chevronLeft" label="Kembali" onClick={onBack} className="-ml-2" />}
        <div className="min-w-0 flex-1 py-2">
          <h1 className="truncate text-[17px] font-semibold tracking-[-.01em] text-on-surface sm:text-lg">{title}</h1>
          {sub && <p className="truncate text-xs text-on-surface-variant">{sub}</p>}
        </div>
        {right}
      </div>
    </header>
  )
}
