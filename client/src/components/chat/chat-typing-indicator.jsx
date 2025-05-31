import { Bot } from "lucide-react"

export default function ChatTypingIndicator() {
  return (
    <div className="flex gap-4 max-w-4xl">
      <div className="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
        <Bot className="w-5 h-5" />
      </div>
      <div className="bg-white rounded-lg rounded-tl-none px-4 py-3 shadow-sm border border-gray-200">
        <div className="flex space-x-1">
          <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
          <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: "0.1s" }}></div>
          <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: "0.2s" }}></div>
        </div>
      </div>
    </div>
  )
}
