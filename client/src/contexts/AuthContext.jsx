"use client"

import { createContext, useState, useContext, useEffect } from "react"
import { useNavigate } from "react-router-dom"
import { API_CHAT_ENDPOINT } from "../config"

const AuthContext = createContext(null)

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : null)
  const [loading, setLoading] = useState(true)
  const navigate = useNavigate()

  // Check if user is already logged in on mount
  useEffect(() => {
    const checkAuthStatus = async () => {
      try {
        const response = await fetch(`${API_CHAT_ENDPOINT}/register`, {
          method: "GET",
          credentials: "include", // Important for cookies
          headers: {
            "Content-Type": "application/json",
          },
        })

        if (response.ok) {
          const userData = await response.json()
          setUser(userData)
        }
      } catch (error) {
        console.error("Authentication check failed:", error)
      } finally {
        setLoading(false)
      }
    }

    checkAuthStatus()
  }, [])

  // Register a new user
  const register = async (name, email, password, password_confirmation) => {
    try {
      const response = await fetch(`${API_CHAT_ENDPOINT}/register`, {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ name, email, password, password_confirmation }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || "Registration failed")
      }

      setUser(data.user)
      navigate("/chat")
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    }
  }

  // Login user
  const login = async (email, password) => {
    try {
      const response = await fetch(`${API_CHAT_ENDPOINT}/login`, {
        method: "POST",
        withCredentials: true,
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, password }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || "Login failed")
      }

      setUser(data.user)
      localStorage.setItem("user", JSON.stringify(data.user))
      navigate("/chat")
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    }
  }

  // Logout user
  const logout = async () => {
    try {
      await fetch(`${API_CHAT_ENDPOINT}/logout`, {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
      })

      localStorage.removeItem("user")
      setUser(null)

      navigate("/login")
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    }
  }

  // Forgot password
  const forgotPassword = async (email) => {
    try {
      const response = await fetch("/api/forgot-password", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.email || data.message || "Failed to send reset link")
      }

      return { success: true, message: data.status }
    } catch (error) {
      return { success: false, error: error.message }
    }
  }

  // Reset password
  const resetPassword = async (token, email, password, password_confirmation) => {
    try {
      const response = await fetch("/api/reset-password", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ token, email, password, password_confirmation }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.email || data.message || "Failed to reset password")
      }

      return { success: true, message: data.status }
    } catch (error) {
      return { success: false, error: error.message }
    }
  }

  const value = {
    user,
    loading,
    register,
    login,
    logout,
    forgotPassword,
    resetPassword,
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (context === null) {
    throw new Error("useAuth must be used within an AuthProvider")
  }
  return context
}
