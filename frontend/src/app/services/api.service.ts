import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Record } from '../models/record.model';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = '/api';
  private credentials = btoa('api:secret');

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    return new HttpHeaders({
      'Authorization': `Basic ${this.credentials}`
    });
  }

  getRecords(): Observable<Record[]> {
    return this.http.get<Record[]>(`${this.apiUrl}/records`, {
      headers: this.getHeaders()
    });
  }

  getRecord(id: number): Observable<Record> {
    return this.http.get<Record>(`${this.apiUrl}/records/${id}`, {
      headers: this.getHeaders()
    });
  }

  updateRecord(id: number, record: Partial<Record>): Observable<Record> {
    return this.http.put<Record>(`${this.apiUrl}/records/${id}`, record, {
      headers: this.getHeaders()
    });
  }

  updateStatus(id: number, status: string): Observable<Record> {
    return this.http.patch<Record>(
      `${this.apiUrl}/records/${id}/status`,
      { status },
      { headers: this.getHeaders() }
    );
  }
}
