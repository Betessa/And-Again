#include <iostream>

using namespace std;

class MyRVector{
public:
    int* data = nullptr;
    size_t n_items = 0;
    size_t n_alloc = 0;

    void push_front(int value){
        if(n_alloc == 0){
            reallocate(1);
        }
        else if(n_alloc == n_items){
            reallocate(2*n_alloc);
        }
        data[n_alloc - n_items -1] = value;
        ++n_items;
    }

    void pop_front(){
        if(n_items == 0) {
            throw std::string("Don't do stupid things.");
        }
        --n_items;
        if(n_items < n_alloc/4){
            reallocate(n_alloc/2);
        }
    }
    int& operator[](size_t i){
        return data[n_alloc - n_items + i];
    }

    void reallocate(size_t new_size){

        int* buffer = new int[new_size];
        for(size_t idx = 0; idx < n_items; ++idx){
            buffer[new_size-1 - idx] = data[n_alloc-1 - idx];
        }
        delete [] data;
        data = buffer;
        n_alloc = new_size;
    }

    void print(){
        for(int i=0 ; i<0 ; i++){
            cout<<n_items<;
        }
    }


};

class MyVector{
public:
    int* data = nullptr;
    size_t n_items = 0;
    size_t n_alloc = 0;

    void push_back(int value){
        if(n_alloc == 0){
            reallocate(1);
        }else if(n_items == n_alloc){
            reallocate(2*n_alloc);
        }
        data[n_items] = value;
        ++n_items;
    }

    void pop_back(){
        if(n_items == 0) throw std::string("Don't do stupid things.");

        --n_items;
        if(n_items < n_alloc/4){
            reallocate(n_alloc/2);
        }
    }

    void reallocate(size_t new_size){

        int* buffer = new int[new_size];
        for(size_t idx = 0; idx < n_items; ++idx){
            buffer[idx] = data[idx];
        }
        delete [] data;
        data = buffer;
        n_alloc = new_size;

    }


};

int main()
{
    print();
    return 0;
}
