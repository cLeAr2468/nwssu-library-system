import React from 'react'
import { Badge } from "@/components/ui/badge"
export default function howitworks() {
  return (
    <section id="how-it-works" className="w-full py-12 md:py-10 lg:py-10 xl:py-10">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center justify-center space-y-4 text-center">
              <div className="space-y-2">
                <h1 className='font-extrabold text-[50px] mb-6'>How it Works</h1>
                <h2 className="text-3xl font-bold tracking-tighter md:text-4xl/tight">Simple to Set Up, Easy to Use</h2>
                <p className="mx-auto max-w-[700px] text-muted-foreground md:text-xl">
                  Get your library online in minutes with our intuitive platform.
                </p>
              </div>
            </div>
            <div className="mx-auto grid max-w-5xl grid-cols-1 gap-6 py-12 md:grid-cols-3">
              <div className="flex flex-col items-center space-y-2 text-center">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                  1
                </div>
                <h3 className="text-xl font-bold">Import Your Catalog</h3>
                <p className="text-muted-foreground">Easily import your existing book catalog or add books manually.</p>
              </div>
              <div className="flex flex-col items-center space-y-2 text-center">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                  2
                </div>
                <h3 className="text-xl font-bold">Set Up Accounts</h3>
                <p className="text-muted-foreground">Create staff and patron accounts with appropriate permissions.</p>
              </div>
              <div className="flex flex-col items-center space-y-2 text-center">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                  3
                </div>
                <h3 className="text-xl font-bold">Start Managing</h3>
                <p className="text-muted-foreground">Begin processing checkouts, returns, and managing your library.</p>
              </div>
            </div>
          </div>
        </section>
  )
}
