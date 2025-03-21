import React, { useRef } from 'react';
import { Button } from '@/components/ui/button';
import { BookOpen } from 'lucide-react';
import Features from '@/components/layout/Features';
import Howitworks from '@/components/layout/Howitworks';
import BodySection from '@/components/layout/BodySection';
import Footer from './footer'

export default function Header() {
  const featuresRef = useRef(null);
  const howItWorksRef = useRef(null);
  const ContuctRef = useRef(null);

  const scrollToSection = (ref) => {
    if (ref.current) {
      ref.current.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <>
      {/* Header Section */}
      <header className="sticky top-0 z-50 w-full border-b bg-gray-900 px-4 md:px-6 text-white">
        <div className="container flex h-16 items-center justify-between">
          <div className="flex items-center gap-2">
            <BookOpen className="h-6 w-6" />
            <span className="md:hidden text-xl font-bold">Library</span>
            <span className="hidden md:block text-xl font-bold">Nwssu Sj Library</span>
          </div>

          <nav className="hidden lg:flex items-center ml-[46%] xl:ml-[46%] lg:ml-[23%] gap-6 lg:gap-5">
            <button onClick={() => scrollToSection(featuresRef)} className="text-lg font-medium hover:text-white/80">Features</button>
            <button onClick={() => scrollToSection(howItWorksRef)} className="text-lg font-medium hover:text-white/80">How it Works</button>
            <button onClick={() => scrollToSection(ContuctRef)} className="text-lg font-medium hover:text-white/80">Contact Us</button>
          </nav>

          <div className="flex items-center gap-4">
            <Button variant="outline" className="hidden md:flex text-black hover:bg-gray-900 hover:text-white font-bold">Log in</Button>
            <Button className='hidden md:block hover:bg-white hover:text-black font-bold'>Register</Button>
            <Button className='md:hidden bg-white text-black hover:bg-gray-900 hover:text-white font-bold'>Get Started</Button>
          </div>
        </div>
      </header>

      <section>
        <BodySection />
      </section>

      <section ref={featuresRef}>
        <Features />
      </section>

      <section ref={howItWorksRef}>
        <Howitworks />
      </section>
      <section ref={ContuctRef}>
        <Footer/>
      </section>
    </>
  );
}
