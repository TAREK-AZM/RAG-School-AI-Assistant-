import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom"
import { ThemeProvider } from "./components/theme-provider"
import { AuthProvider } from "./contexts/AuthContext"
import Layout from "./components/layout"
import ChatPage from "./components/chat/chat-page"
import AdminDashboard from "./components/admin/admin-dashboard"
import LoginPage from "./auth/LoginPage"
import SignupPage from "./auth/SignupPage"
import ForgotPasswordPage from "./auth/ForgotPasswordPage"
import ResetPasswordPage from "./auth/ResetPasswordPage"
import ProtectedRoute from "./auth/ProtectedRoute"

function App() {
  return (
    <ThemeProvider defaultTheme="light">
      <Router>
        <AuthProvider>
          <Routes>
            {/* Auth Routes */}
            <Route path="/login" element={<LoginPage />} />
            <Route path="/signup" element={<SignupPage />} />
            <Route path="/forgot-password" element={<ForgotPasswordPage />} />
            <Route path="/reset-password" element={<ResetPasswordPage />} />

            {/* Protected Routes */}
            <Route element={<ProtectedRoute />}>
              <Route path="/" element={<Layout />}>
                <Route index element={<Navigate to="/chat" replace />} />
                <Route path="chat" element={<ChatPage />} />
                <Route path="admin" element={<AdminDashboard />} />
              </Route>
            </Route>
          </Routes>
        </AuthProvider>
      </Router>
    </ThemeProvider>
  )
}

export default App
