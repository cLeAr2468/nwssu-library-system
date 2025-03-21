import React from 'react'
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import {CheckCircle2, ArrowRight } from "lucide-react"
import Imageslogo from '../../assets/logo.jpg'
export default function bodysection() {
  return (
    <section className="w-full py-12 md:py-2 lg:py-3 xl:py-4 md:px-6">
    <div className="container px-4 md:px-6">
      <div className="grid gap-6 lg:grid-cols-2 lg:gap-12 xl:grid-cols-2">
        <div className="flex flex-col justify-center space-y-4">
          <div className="space-y-2">
            <Badge className="inline-flex">New Release</Badge>
            <h1 className="text-3xl font-bold tracking-tighter sm:text-5xl xl:text-6xl/none">
              Manage Your Library With Ease
            </h1>
            <p className="max-w-[600px] text-muted-foreground md:text-xl">
              Streamline your library operations, engage readers, and gain valuable insights with our all-in-one
              library management system.
            </p>
          </div>
          <div className="flex flex-col gap-2 min-[400px]:flex-row">
            <Button size="lg" className="gap-1">
              Get Started! <ArrowRight className="h-4 w-4" />
            </Button>
          </div>
          <div className="flex items-center gap-2 text-sm">
            <CheckCircle2 className="h-4 w-4 text-primary" />
            <span>No credit card required</span>
          </div>
        </div>
        <div className="flex items-center justify-center">
          <div className="relative w-full aspect-video overflow-hidden rounded-xl">
            <img src={Imageslogo} alt="" />
          </div>
        </div>
      </div>
    </div>
  </section>
  )
}
