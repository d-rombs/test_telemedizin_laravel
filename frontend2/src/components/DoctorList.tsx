import React, { useState, useEffect } from 'react';
import { Doctor, Specialization } from '../types';
import { getDoctors, searchDoctors, getSpecializations } from '../services/api';

interface DoctorListProps {
  onSelectDoctor: (doctor: Doctor) => void;
}

const DoctorList: React.FC<DoctorListProps> = ({ onSelectDoctor }) => {
  const [doctors, setDoctors] = useState<Doctor[]>([]);
  const [specializations, setSpecializations] = useState<Specialization[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedSpecialization, setSelectedSpecialization] = useState<number | null>(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const [doctorsData, specializationsData] = await Promise.all([
          getDoctors(),
          getSpecializations()
        ]);
        setDoctors(doctorsData);
        setSpecializations(specializationsData);
        setError(null);
      } catch (err) {
        setError('Fehler beim Laden der Daten. Bitte versuchen Sie es später erneut.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const handleSearch = async () => {
    if (!searchQuery) {
      try {
        const doctorsData = await getDoctors();
        setDoctors(doctorsData);
        setError(null);
      } catch (err) {
        setError('Fehler bei der Suche. Bitte versuchen Sie es später erneut.');
        console.error(err);
      }
      return;
    }

    try {
      setLoading(true);
      const results = await searchDoctors(searchQuery);
      setDoctors(results);
      setError(null);
    } catch (err) {
      setError('Fehler bei der Suche. Bitte versuchen Sie es später erneut.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const filteredDoctors = selectedSpecialization
    ? doctors.filter(doctor => doctor.specialization_id === selectedSpecialization)
    : doctors;

  return (
    <div className="doctor-list">
      <h2>Ärzte</h2>
      
      <div className="search-container">
        <input
          type="text"
          placeholder="Suche nach Ärzten..."
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
        />
        <button onClick={handleSearch}>Suchen</button>
      </div>

      <div className="filter-container">
        <label>
          Fachgebiet:
          <select
            value={selectedSpecialization || ''}
            onChange={(e) => setSelectedSpecialization(e.target.value ? Number(e.target.value) : null)}
          >
            <option value="">Alle Fachgebiete</option>
            {specializations.map(specialization => (
              <option key={specialization.id} value={specialization.id}>
                {specialization.name}
              </option>
            ))}
          </select>
        </label>
      </div>

      {loading && <p>Laden...</p>}
      {error && <p className="error">{error}</p>}
      
      <ul className="doctors">
        {filteredDoctors.map(doctor => (
          <li key={doctor.id} onClick={() => onSelectDoctor(doctor)} className="doctor-item">
            <h3>{doctor.name}</h3>
            <p>
              Fachgebiet: {
                specializations.find(s => s.id === doctor.specialization_id)?.name || 
                doctor.specialization?.name || 
                'Unbekannt'
              }
            </p>
          </li>
        ))}
        {!loading && filteredDoctors.length === 0 && (
          <p>Keine Ärzte gefunden.</p>
        )}
      </ul>
    </div>
  );
};

export default DoctorList; 