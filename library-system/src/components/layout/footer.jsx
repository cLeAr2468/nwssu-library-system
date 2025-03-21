import React from 'react'
import { BookOpen } from "lucide-react";

export default function Footer() {
  return (
    <footer className="w-full border-t bg-gray-900 py-6 md:py-12 text-white">
      <div className="container px-4 md:px-6">
        <div className="grid grid-cols-2 gap-8 md:grid-cols-3 lg:grid-cols-4 ml-5 justify-center">
          <div className="space-y-4">
            <div className="flex items-center gap-2">
              <BookOpen className="h-6 w-6" />
              <span className="text-xl font-bold">LibraryOS</span>
            </div>
            <p className="text-sm text-muted-foreground text-white">Modern library management for the digital age.</p>
            <div className="flex gap-5 ">
              <a href="#" className="text-muted-foreground hover:text-foreground text-white">
                <span className="sr-only">Twitter</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="h-5 w-5">
                  <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" />
                </svg>
              </a>
              <a href="#" className="text-muted-foreground hover:text-foreground text-white">
                <span className="sr-only">Facebook</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="h-5 w-5">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                </svg>
              </a>
              <a href="#" className="text-muted-foreground hover:text-foreground text-white">
                <span className="sr-only">LinkedIn</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="h-5 w-5">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                  <rect width="4" height="12" x="2" y="9" />
                  <circle cx="4" cy="4" r="2" />
                </svg>
              </a>
            </div>
          </div>

          {['Product', 'Company'].map((section, index) => (
            <div key={index} className="space-y-4">
              <h3 className="text-lg font-semibold ">{section}</h3>
              <ul className="">
                {['Features', 'Pricing', 'Integrations', 'Roadmap'].map((item, idx) => (
                  <li key={idx}>
                    <a href="#" className="text-sm text-muted-foreground hover:text-foreground text-white">{item}</a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-8 border-t pt-8 flex flex-col md:flex-row justify-between items-center">
          <p className="text-xs text-muted-foreground mb-4 md:mb-0 text-white">&copy; {new Date().getFullYear()} LibraryOS. All rights reserved.</p>
          <div className="flex gap-4">
            <a href="#" className="text-xs text-muted-foreground hover:text-foreground text-white">Terms of Service</a>
            <a href="#" className="text-xs text-muted-foreground hover:text-foreground text-white">Privacy Policy</a>
            <a href="#" className="text-xs text-muted-foreground hover:text-foreground text-white">Cookie Policy</a>
          </div>
        </div>
      </div>
    </footer>
  );
}
