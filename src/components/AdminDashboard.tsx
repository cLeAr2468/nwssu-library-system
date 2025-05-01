import { useState } from 'react';
import { Link } from 'react-router-dom';
import {
  Users,
  BookOpen,
  Bookmark,
  Library,
  Coins,
  Bell,
  ArrowUp,
  ArrowDown,
  Warning,
} from 'lucide-react';
import { Button } from './ui/button';
import { Card, CardContent, CardHeader, CardTitle } from './ui/card';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from './ui/sheet';
import { Input } from './ui/input';
import { Textarea } from './ui/textarea';
import { Label } from './ui/label';

interface DashboardStats {
  totalUsers: number;
  pendingAccounts: number;
  totalBooks: number;
  totalCopies: number;
  reservedBooks: number;
  borrowedBooks: number;
  pendingFines: number;
}

export function AdminDashboard() {
  const [isAnnouncementOpen, setIsAnnouncementOpen] = useState(false);
  const [stats] = useState<DashboardStats>({
    totalUsers: 1500,
    pendingAccounts: 12,
    totalBooks: 5000,
    totalCopies: 7500,
    reservedBooks: 45,
    borrowedBooks: 120,
    pendingFines: 8,
  });

  const statCards = [
    {
      title: 'Total Users',
      value: stats.totalUsers,
      icon: Users,
      trend: { value: 12, isPositive: true },
      href: '/admin/users',
      color: 'primary',
    },
    {
      title: 'Pending Accounts',
      value: stats.pendingAccounts,
      icon: Warning,
      trend: { value: 'Needs Review', isPositive: false },
      href: '/admin/pending',
      color: 'yellow',
    },
    {
      title: 'Total Books',
      value: stats.totalBooks,
      subtitle: `${stats.totalCopies} copies`,
      icon: BookOpen,
      trend: { value: 8, isPositive: true },
      href: '/admin/catalog',
      color: 'blue',
    },
    {
      title: 'Reserved Books',
      value: stats.reservedBooks,
      icon: Bookmark,
      trend: { value: 'Active Reservations', isPositive: true },
      href: '/admin/reservations',
      color: 'purple',
    },
    {
      title: 'Borrowed Books',
      value: stats.borrowedBooks,
      icon: Library,
      trend: { value: 3, isPositive: false },
      href: '/admin/borrowed',
      color: 'green',
    },
    {
      title: 'Pending Fines',
      value: stats.pendingFines,
      icon: Coins,
      trend: { value: 5, isPositive: false },
      href: '/admin/fines',
      color: 'red',
    },
  ];

  return (
    <div className="p-4 lg:ml-64 mt-14">
      <div className="p-4 rounded-lg">
        {/* Header Section */}
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p className="text-gray-600">Welcome back, Administrator</p>
          </div>
          <div className="mt-4 md:mt-0">
            <Sheet open={isAnnouncementOpen} onOpenChange={setIsAnnouncementOpen}>
              <SheetTrigger asChild>
                <Button className="bg-primary-600 hover:bg-primary-700">
                  <Bell className="mr-2 h-4 w-4" />
                  Add Announcement
                </Button>
              </SheetTrigger>
              <SheetContent>
                <SheetHeader>
                  <SheetTitle>Add New Announcement</SheetTitle>
                </SheetHeader>
                <div className="grid gap-4 py-4">
                  <div className="grid gap-2">
                    <Label htmlFor="title">Title</Label>
                    <Input id="title" placeholder="Enter announcement title" />
                  </div>
                  <div className="grid gap-2">
                    <Label htmlFor="content">Content</Label>
                    <Textarea
                      id="content"
                      placeholder="Enter announcement content"
                      className="min-h-[200px]"
                    />
                  </div>
                  <Button type="submit">Publish Announcement</Button>
                </div>
              </SheetContent>
            </Sheet>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
          {statCards.map((card) => (
            <Link to={card.href} key={card.title}>
              <Card className="hover:shadow-md transition duration-300">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium text-gray-600">
                    {card.title}
                  </CardTitle>
                  <div className={`bg-${card.color}-100 p-2 rounded-full`}>
                    <card.icon className={`h-4 w-4 text-${card.color}-600`} />
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{card.value}</div>
                  {card.subtitle && (
                    <p className="text-xs text-gray-500">{card.subtitle}</p>
                  )}
                  <div className="flex items-center text-xs mt-2">
                    {typeof card.trend.value === 'number' ? (
                      <>
                        {card.trend.isPositive ? (
                          <ArrowUp className="h-3 w-3 text-green-500 mr-1" />
                        ) : (
                          <ArrowDown className="h-3 w-3 text-red-500 mr-1" />
                        )}
                        <span
                          className={
                            card.trend.isPositive
                              ? 'text-green-500'
                              : 'text-red-500'
                          }
                        >
                          {card.trend.value}%
                        </span>
                      </>
                    ) : (
                      <span className="text-gray-500">{card.trend.value}</span>
                    )}
                  </div>
                </CardContent>
              </Card>
            </Link>
          ))}
        </div>

        {/* Charts Section */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <Card>
            <CardHeader>
              <CardTitle>Borrowing Trends</CardTitle>
            </CardHeader>
            <CardContent>
              {/* Add Chart.js or other charting library here */}
              <div className="h-[300px] flex items-center justify-center text-gray-500">
                Chart will be implemented here
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Book Categories Distribution</CardTitle>
            </CardHeader>
            <CardContent>
              {/* Add Chart.js or other charting library here */}
              <div className="h-[300px] flex items-center justify-center text-gray-500">
                Chart will be implemented here
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Recent Activity Section */}
        <Card>
          <CardHeader>
            <CardTitle>Recent Activity</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Activity
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      User
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {/* Sample data - Replace with actual data */}
                  <tr>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      Book Borrowed
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      John Doe
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      2024-03-15
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
} 