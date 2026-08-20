import type { SVGProps } from 'react'

export type IconName =
  | 'home'
  | 'wallet'
  | 'notice'
  | 'comment'
  | 'person'
  | 'lock'
  | 'logout'
  | 'chevronRight'
  | 'chevronLeft'
  | 'camera'
  | 'send'
  | 'check'
  | 'close'
  | 'phone'
  | 'refresh'
  | 'calendar'
  | 'alert'
  | 'sun'
  | 'moon'
  | 'monitor'
  | 'edit'
  | 'info'

const paths: Record<IconName, React.ReactNode> = {
  home: <path d="M12 3l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1V9.5L12 3z" />,
  wallet: (
    <path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6zm3 2v2h10V8H7zm0 5v2h4v-2H7zm8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" />
  ),
  notice: (
    <path d="M6.5 3.75h11a2.75 2.75 0 0 1 2.75 2.75v11a2.75 2.75 0 0 1-2.75 2.75h-11a2.75 2.75 0 0 1-2.75-2.75v-11A2.75 2.75 0 0 1 6.5 3.75Zm1.25 5h8.5m-8.5 3.5h8.5m-8.5 3.5h5.5" fill="none" stroke="currentColor" strokeWidth="1.75" strokeLinecap="round" />
  ),
  comment: <path d="M4 5h16v11H9l-5 4V5zm3 4v2h8V9H7zm0 3v2h5v-2H7z" />,
  person: (
    <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5z" />
  ),
  lock: (
    <path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1zm2 0h6V7a3 3 0 0 0-6 0v3zm3 5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" />
  ),
  logout: (
    <path d="M10 4v2h7v12h-7v2h9V4h-9zm-4.3 5l-1.4 1.4L6.9 13H14v2H6.9l2.6 2.6-1.4 1.4L2.7 14l3.4-3.4-.4-.6z" />
  ),
  chevronRight: <path d="M9.3 6.7L14.6 12l-5.3 5.3 1.4 1.4 6.7-6.7-6.7-6.7-1.4 1.4z" />,
  chevronLeft: <path d="M14.7 6.7L9.4 12l5.3 5.3-1.4 1.4-6.7-6.7 6.7-6.7 1.4 1.4z" />,
  camera: (
    <path d="M9 3l-1.5 2H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.5L15 3H9zm3 14a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
  ),
  send: <path d="M3 20v-6l8-2-8-2V4l18 8-18 8z" />,
  check: <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" />,
  close: <path d="M6.4 4.7L12 10.3l5.6-5.6 1.4 1.4L13.4 12l5.6 5.6-1.4 1.4L12 13.4l-5.6 5.6-1.4-1.4 5.6-5.6-5.6-5.6 1.4-1.4z" />,
  phone: (
    <path d="M6.6 3h3l1.4 4-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2 4 1.4v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.2 2 2 0 0 1 6.6 3z" />
  ),
  refresh: (
    <path d="M12 5a7 7 0 1 0 6.9 5h2.1a9 9 0 1 1-9-9c2.5 0 4.8 1 6.4 2.7L21 2v6h-6l2.2-2.2A7 7 0 0 0 12 5z" />
  ),
  calendar: (
    <path d="M7 3v2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2V3h-2v2H9V3H7zM5 9h14v10H5V9zm3 3v2h2v-2H8zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H8zm4 0v2h2v-2h-2z" />
  ),
  alert: (
    <path d="M12 3L1.5 20h21L12 3zm1 14h-2v-2h2v2zm0-4h-2v-5h2v5z" />
  ),
  sun: (
    <path d="M12 5a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm0-3l1.5 2h-3L12 2zm8 9l2 1.5-2 1.5V11zM4 11v3l-2-1.5L4 11zm13.7-5.7l.6 2.8-2.1-.1 1.5-2.7zM6.6 9.3l-2-1.6 2-3 .8 3.5-2.2 1.1zm10.2 5.6h2.9l-1 2.4-2.4-1 .5-1.4zM7.4 14.9l-2.3 1.2 1 2.5 3.5-1.6-2.2-2.1z" />
  ),
  moon: <path d="M12 3a9 9 0 1 0 9 9c0-.5 0-1-.1-1.4A7 7 0 0 1 12 3z" />,
  monitor: (
    <path d="M3 5h18v12H3V5zm2 2v8h14V7H5zm4 12h6v2H9v-2z" />
  ),
  edit: (
    <path d="M4 20h4l10.5-10.5-4-4L4 16v4zm4.5-1.5H6v-2.5l8.5-8.5 2.5 2.5-8.5 8.5z" />
  ),
  info: (
    <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 5h-2v2h2V7zm0 4h-2v6h2v-6z" />
  ),
}

interface Props extends SVGProps<SVGSVGElement> {
  name: IconName
  size?: number
}

export function Icon({ name, size = 24, ...rest }: Props) {
  return (
    <svg
      viewBox="0 0 24 24"
      width={size}
      height={size}
      fill="currentColor"
      aria-hidden="true"
      {...rest}
    >
      {paths[name]}
    </svg>
  )
}
