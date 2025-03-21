import React from 'react'
import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { BookOpen, Search, Clock, BarChart3, Bell, Users} from "lucide-react"

export default function features() {
  return (
    
    <section id="features" className="w-full py-12 md:py-10 lg:py-10 xl:py-10 bg-muted/40">
    <div className="container px-4 md:px-6">
      <div className="flex flex-col items-center justify-center space-y-4 text-center">
        <div className="space-y-2">
            <h1 className='font-extrabold text-[50px] mb-6'>FEATURES</h1>
          <h2 className="text-3xl font-bold tracking-tighter md:text-4xl/tight">
            Everything You Need to Run Your Library
          </h2>
          <p className="mx-auto max-w-[700px] text-muted-foreground md:text-xl">
            Our comprehensive solution helps you manage books, patrons, and operations efficiently.
          </p>
        </div>
      </div>
      <div className="mx-auto grid max-w-5xl grid-cols-1 gap-6 py-12 md:grid-cols-2 lg:grid-cols-3">
        <Card>
          <CardHeader>
            <Search className="h-10 w-10 text-primary mb-2" />
            <CardTitle>Smart Catalog</CardTitle>
            <CardDescription>Powerful search and filtering to find any book in seconds.</CardDescription>
          </CardHeader>
        </Card>
        <Card>
          <CardHeader>
            <Clock className="h-10 w-10 text-primary mb-2" />
            <CardTitle>Automated Checkouts</CardTitle>
            <CardDescription>Streamline borrowing and returns with automated processes.</CardDescription>
          </CardHeader>
        </Card>
        <Card>
          <CardHeader>
            <BarChart3 className="h-10 w-10 text-primary mb-2" />
            <CardTitle>Insightful Analytics</CardTitle>
            <CardDescription>Track usage patterns and make data-driven decisions.</CardDescription>
          </CardHeader>
        </Card>
        <Card>
          <CardHeader>
            <Bell className="h-10 w-10 text-primary mb-2" />
            <CardTitle>Smart Notifications</CardTitle>
            <CardDescription>Automated reminders for due dates and reservations.</CardDescription>
          </CardHeader>
        </Card>
        <Card>
          <CardHeader>
            <Users className="h-10 w-10 text-primary mb-2" />
            <CardTitle>Patron Management</CardTitle>
            <CardDescription>Easily manage member accounts, history, and preferences.</CardDescription>
          </CardHeader>
        </Card>
        <Card>
          <CardHeader>
            <BookOpen className="h-10 w-10 text-primary mb-2" />
            <CardTitle>Digital Reading</CardTitle>
            <CardDescription>Offer e-books and digital resources to your patrons.</CardDescription>
          </CardHeader>
        </Card>
      </div>
    </div>
  </section>
  )
}
